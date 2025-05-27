<?php

namespace App\Services;

use App\Models\BalanceMutation;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\StoreBalance;
use App\Models\ProductUnit;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    /**
     * Membuat purchase order baru
     *
     * @param array $data
     * @return PurchaseOrder
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Create purchase order
            $purchase = PurchaseOrder::create([
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'invoice_number' => 'PO-' . date('YmdHis'),
                'total_amount' => $data['total_amount'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'final_amount' => $data['final_amount'],
                'payment_type' => $data['payment_type'],
                'reference_number' => $data['reference_number'],
                'status' => 'pending',
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes']
            ]);

            // Create purchase order items dan update stock
            $this->createPurchaseItems($purchase, $data['items']);

            // Jika pembayaran cash, atur balance
            if ($data['payment_type'] === 'cash') {
                $this->processCashPayment($purchase, $data['store_id']);
            }

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update purchase order
     *
     * @param PurchaseOrder $purchase
     * @param array $data
     * @return PurchaseOrder
     */
    public function update(PurchaseOrder $purchase, array $data)
    {
        if ($purchase->status !== 'pending') {
            throw new \Exception('Only pending purchase orders can be edited');
        }

        DB::beginTransaction();
        try {
            // Rollback existing items
            $this->rollbackPurchaseItems($purchase);

            // Update purchase order
            $purchase->update([
                'supplier_id' => $data['supplier_id'],
                'total_amount' => $data['total_amount'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'final_amount' => $data['final_amount'],
                'payment_type' => $data['payment_type'],
                'reference_number' => $data['reference_number'],
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes']
            ]);

            // Delete existing items
            $purchase->items()->delete();

            // Create new items dan update stock
            $this->createPurchaseItems($purchase, $data['items']);

            // Jika payment type berubah, atur balance
            if ($purchase->payment_type !== $data['payment_type']) {
                $this->handlePaymentTypeChange($purchase, $data);
            }

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update status purchase order
     *
     * @param PurchaseOrder $purchase
     * @param string $status
     * @return PurchaseOrder
     */
    public function updateStatus(PurchaseOrder $purchase, string $status)
    {
        if ($purchase->status !== 'pending') {
            throw new \Exception('Only pending purchase orders can be updated');
        }

        DB::beginTransaction();
        try {
            if ($status === 'cancelled') {
                $this->cancelPurchaseOrder($purchase);
            }

            $purchase->update(['status' => $status]);

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Membuat item purchase order dan update stock
     *
     * @param PurchaseOrder $purchase
     * @param array $items
     * @return void
     */
    private function createPurchaseItems(PurchaseOrder $purchase, array $items)
    {
        foreach ($items as $item) {
            // Create purchase item
            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'unit_id' => $item['unit_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
                'discount' => $item['discount'] ?? 0
            ]);

            // Update stock
            $productUnit = ProductUnit::where('product_id', $item['product_id'])
                ->where('unit_id', $item['unit_id'])
                ->first();

            if (!$productUnit) {
                // Jika product unit belum ada, buat baru
                $productUnit = ProductUnit::create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'stock' => $item['quantity'],
                    'purchase_price' => $item['unit_price'],
                    'selling_price' => $item['unit_price'] * 1.1, // Set selling price 10% lebih tinggi dari purchase price
                    'conversion_factor' => 1 // Set default conversion factor ke 1
                ]);
            } else {
                // Jika sudah ada, increment stock
                $productUnit->increment('stock', $item['quantity']);
            }

            // Create stock history
            StockHistory::create([
                'store_id' => $purchase->store_id,
                'product_unit_id' => $productUnit->id,
                'reference_type' => 'App\Models\PurchaseOrder',
                'reference_id' => $purchase->id,
                'type' => 'in',
                'quantity' => $item['quantity'],
                'remaining_stock' => $productUnit->stock,
                'notes' => "Purchase Order #{$purchase->invoice_number}"
            ]);
        }
    }

    /**
     * Rollback item purchase order dan update stock
     *
     * @param PurchaseOrder $purchase
     * @return void
     */
    private function rollbackPurchaseItems(PurchaseOrder $purchase)
    {
        foreach ($purchase->items as $item) {
            $productUnit = ProductUnit::where('product_id', $item->product_id)
                ->where('unit_id', $item->unit_id)
                ->first();

            $productUnit->decrement('stock', $item->quantity);

            // Create stock history for rollback
            StockHistory::create([
                'store_id' => $purchase->store_id,
                'product_unit_id' => $productUnit->id,
                'reference_type' => 'App\Models\PurchaseOrder',
                'reference_id' => $purchase->id,
                'type' => 'out',
                'quantity' => $item->quantity,
                'remaining_stock' => $productUnit->stock,
                'notes' => "Rollback Update Purchase Order #{$purchase->invoice_number}"
            ]);
        }
    }

    /**
     * Proses pembayaran cash
     *
     * @param PurchaseOrder $purchase
     * @param int $storeId
     * @return void
     */
    private function processCashPayment(PurchaseOrder $purchase, int $storeId)
    {
        $store = Store::findOrFail($storeId);
        $balance = $this->getOrCreateStoreBalance($store);

        $previousBalance = $balance->cash_amount;
        $currentBalance = $previousBalance - $purchase->final_amount;

        // Create balance mutation
        BalanceMutation::create([
            'store_id' => $store->id,
            'type' => 'out',
            'payment_method' => 'cash',
            'amount' => $purchase->final_amount,
            'previous_balance' => $previousBalance,
            'current_balance' => $currentBalance,
            'source_type' => 'App\Models\PurchaseOrder',
            'source_id' => $purchase->id,
            'notes' => "Purchase Order #{$purchase->invoice_number}",
            'created_by' => auth()->id()
        ]);

        // Update store balance
        $balance->update([
            'cash_amount' => $currentBalance,
            'last_updated_by' => auth()->id()
        ]);
    }

    /**
     * Batalkan purchase order
     *
     * @param PurchaseOrder $purchase
     * @return void
     */
    private function cancelPurchaseOrder(PurchaseOrder $purchase)
    {
        // Rollback stock jika cancelled
        foreach ($purchase->items as $item) {
            $productUnit = ProductUnit::where('product_id', $item->product_id)
                ->where('unit_id', $item->unit_id)
                ->first();
            $productUnit->decrement('stock', $item->quantity);

            // Create stock history
            StockHistory::create([
                'store_id' => $purchase->store_id,
                'product_unit_id' => $productUnit->id,
                'reference_type' => 'App\Models\PurchaseOrder',
                'reference_id' => $purchase->id,
                'type' => 'out',
                'quantity' => $item->quantity,
                'remaining_stock' => $productUnit->stock,
                'notes' => "Cancel Purchase Order #{$purchase->invoice_number}"
            ]);
        }

        // Jika payment type cash
        if ($purchase->payment_type === 'cash') {
            $store = $purchase->store;
            $balance = $store->balance;

            if ($balance) {
                $previousBalance = $balance->cash_amount;
                $currentBalance = $previousBalance + $purchase->final_amount;

                // Create balance mutation
                BalanceMutation::create([
                    'store_id' => $store->id,
                    'type' => 'in',
                    'payment_method' => 'cash',
                    'amount' => $purchase->final_amount,
                    'previous_balance' => $previousBalance,
                    'current_balance' => $currentBalance,
                    'source_type' => 'App\Models\PurchaseOrder',
                    'source_id' => $purchase->id,
                    'notes' => "Cancel Purchase Order #{$purchase->invoice_number}",
                    'created_by' => auth()->id()
                ]);

                // Update store balance
                $balance->update([
                    'cash_amount' => $currentBalance,
                    'last_updated_by' => auth()->id()
                ]);
            }
        }
    }

    /**
     * Tangani perubahan jenis pembayaran
     *
     * @param PurchaseOrder $purchase
     * @param array $data
     * @return void
     */
    private function handlePaymentTypeChange(PurchaseOrder $purchase, array $data)
    {
        if ($purchase->payment_type === 'cash') {
            // Revert previous cash payment
            $store = $purchase->store;
            $balance = $store->balance;

            if ($balance) {
                $previousBalance = $balance->cash_amount;
                $currentBalance = $previousBalance + $purchase->final_amount;

                BalanceMutation::create([
                    'store_id' => $store->id,
                    'type' => 'in',
                    'payment_method' => 'cash',
                    'amount' => $purchase->final_amount,
                    'previous_balance' => $previousBalance,
                    'current_balance' => $currentBalance,
                    'source_type' => 'App\Models\PurchaseOrder',
                    'source_id' => $purchase->id,
                    'notes' => "Revert cash payment Purchase Order #{$purchase->invoice_number}",
                    'created_by' => auth()->id()
                ]);

                $balance->update([
                    'cash_amount' => $currentBalance,
                    'last_updated_by' => auth()->id()
                ]);
            }
        }

        if ($data['payment_type'] === 'cash') {
            // Add new cash payment
            $store = $purchase->store;
            $balance = $this->getOrCreateStoreBalance($store);

            $previousBalance = $balance->cash_amount;
            $currentBalance = $previousBalance - $data['final_amount'];

            BalanceMutation::create([
                'store_id' => $store->id,
                'type' => 'out',
                'payment_method' => 'cash',
                'amount' => $data['final_amount'],
                'previous_balance' => $previousBalance,
                'current_balance' => $currentBalance,
                'source_type' => 'App\Models\PurchaseOrder',
                'source_id' => $purchase->id,
                'notes' => "New cash payment Purchase Order #{$purchase->invoice_number}",
                'created_by' => auth()->id()
            ]);

            $balance->update([
                'cash_amount' => $currentBalance,
                'last_updated_by' => auth()->id()
            ]);
        }
    }

    /**
     * Ambil atau buat saldo toko
     *
     * @param Store $store
     * @return StoreBalance
     */
    private function getOrCreateStoreBalance(Store $store)
    {
        $balance = $store->balance;

        if (!$balance) {
            // Buat record balance baru jika belum ada
            $balance = new StoreBalance([
                'cash_amount' => 0,
                'non_cash_amount' => 0,
                'last_updated_by' => auth()->id()
            ]);
            $store->balance()->save($balance);
        }

        return $balance;
    }
}
