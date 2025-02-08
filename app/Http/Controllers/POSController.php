<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use DB;

class POSController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $latestInvoice = Transaction::latest()->first();
        $invoiceNumber = $this->generateInvoiceNumber($latestInvoice);

        return view('pos.index', compact('customers', 'invoiceNumber'));
    }

    public function getProduct(Request $request)
    {
        $barcode = $request->barcode;

        $product = Product::with(['units', 'tax', 'discount', 'priceTiers'])
            ->where('barcode', $barcode)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,transfer',
            'reference_number' => 'required_if:payment_type,transfer'
        ]);

        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'cashier_id' => auth()->id(),
                'total_amount' => $request->total_amount,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount,
                'final_amount' => $request->final_amount,
                'payment_type' => $request->payment_type,
                'reference_number' => $request->reference_number,
                'status' => 'success',
                'invoice_date' => now()
            ]);

            // Create transaction items
            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'discount' => $item['discount'] ?? 0
                ]);

                // Update inventory
                $inventory = Inventory::where('product_id', $item['product_id'])
                    ->where('unit_id', $item['unit_id'])
                    ->first();

                if ($inventory) {
                    $inventory->quantity -= $item['quantity'];
                    $inventory->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'message' => 'Transaksi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printInvoice(Transaction $transaction)
    {
        return view('pos.invoice', compact('transaction'));
    }

    private function generateInvoiceNumber($latestInvoice)
    {
        $prefix = 'INV' . date('Ymd');

        if (!$latestInvoice) {
            return $prefix . '0001';
        }

        $lastNumber = substr($latestInvoice->invoice_number, -4);
        $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }
}
