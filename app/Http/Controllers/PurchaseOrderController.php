<?php

namespace App\Http\Controllers;

use App\Models\BalanceMutation;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchases = PurchaseOrder::with('supplier')
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->latest()
            ->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        // Cek apakah user adalah admin
        if (auth()->user()->role === 'admin') {
            // Admin bisa lihat semua data
            $suppliers = Supplier::all();
            $products = Product::with(['units' => function($q) {
                $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
            }])->get();
        } else {
            // User biasa hanya bisa lihat data store-nya
            $suppliers = Supplier::where('store_id', auth()->user()->store_id)->get();
            $products = Product::where('store_id', auth()->user()->store_id)
                ->with(['units' => function($q) {
                    $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                }])
                ->get();
        }

        // Untuk admin, tambahkan daftar store untuk dipilih
        $stores = auth()->user()->role === 'admin' ? Store::all() : null;

        return view('purchases.create', compact('suppliers', 'products', 'stores'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_type' => 'required|in:cash,transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Tentukan store_id
            $store_id = auth()->user()->role === 'admin'
                ? $request->store_id
                : auth()->user()->store_id;

            // Create purchase order
            $purchase = PurchaseOrder::create([
                'store_id' => $store_id,
                'supplier_id' => $request->supplier_id,
                'invoice_number' => 'PO-' . date('YmdHis'),
                'total_amount' => $request->total_amount,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'final_amount' => $request->final_amount,
                'payment_type' => $request->payment_type,
                'reference_number' => $request->reference_number,
                'status' => 'pending',
                'purchase_date' => $request->purchase_date,
                'notes' => $request->notes
            ]);

            // Create purchase order items
            foreach ($request->items as $item) {
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

                $productUnit->increment('stock', $item['quantity']);

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

            // Jika pembayaran cash
            if ($request->payment_type === 'cash') {
                $store = Store::findOrFail($store_id);

                // Cek dan buat balance jika belum ada
                $balance = $store->balance ?? $store->balance()->create([
                    'cash_amount' => 0,
                    'non_cash_amount' => 0,
                    'last_updated_by' => auth()->id()
                ]);

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

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchase)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' && auth()->user()->store_id !== $purchase->store_id) {
            return redirect()->route('purchases.index')
                ->with('error', 'You do not have permission to view this purchase order');
        }

        $purchase->load(['supplier', 'items.product', 'items.unit', 'store']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(PurchaseOrder $purchase)
    {
        // Cek akses dan status
        if (auth()->user()->role !== 'admin' && auth()->user()->store_id !== $purchase->store_id) {
            return redirect()->route('purchases.index')
                ->with('error', 'You do not have permission to edit this purchase order');
        }

        if ($purchase->status !== 'pending') {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Only pending purchase orders can be edited');
        }

        // Get data berdasarkan role
        if (auth()->user()->role === 'admin') {
            $suppliers = Supplier::all();
            $products = Product::with(['units' => function($q) {
                $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
            }])->get();
            $stores = Store::all();
        } else {
            $suppliers = Supplier::where('store_id', auth()->user()->store_id)->get();
            $products = Product::where('store_id', auth()->user()->store_id)
                ->with(['units' => function($q) {
                    $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                }])
                ->get();
            $stores = null;
        }

        return view('purchases.edit', compact('purchase', 'suppliers', 'products', 'stores'));
    }

    public function update(Request $request, PurchaseOrder $purchase)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' && auth()->user()->store_id !== $purchase->store_id) {
            return redirect()->route('purchases.index')
                ->with('error', 'You do not have permission to update this purchase order');
        }

        // Jika update status
        if ($request->has('status')) {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:completed,cancelled'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            if ($purchase->status !== 'pending') {
                return back()->with('error', 'Only pending purchase orders can be updated');
            }

            DB::beginTransaction();
            try {
                if ($request->status === 'cancelled' && $purchase->status === 'pending') {
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

                $purchase->update(['status' => $request->status]);

                DB::commit();
                return redirect()->route('purchases.show', $purchase)
                    ->with('success', 'Purchase order status updated successfully');
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'Error updating purchase order: ' . $e->getMessage());
            }
        }

        // Jika update purchase order (edit)
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'Only pending purchase orders can be edited');
        }

        // Set validation rules
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_type' => 'required|in:cash,transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Rollback existing items
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

            // Update purchase order
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'total_amount' => $request->total_amount,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'final_amount' => $request->final_amount,
                'payment_type' => $request->payment_type,
                'reference_number' => $request->reference_number,
                'purchase_date' => $request->purchase_date,
                'notes' => $request->notes
            ]);

            // Delete existing items
            $purchase->items()->delete();

            // Create new items
            foreach ($request->items as $item) {
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

                $productUnit->increment('stock', $item['quantity']);

                // Create stock history
                StockHistory::create([
                    'store_id' => $purchase->store_id,
                    'product_unit_id' => $productUnit->id,
                    'reference_type' => 'App\Models\PurchaseOrder',
                    'reference_id' => $purchase->id,
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'remaining_stock' => $productUnit->stock,
                    'notes' => "Update Purchase Order #{$purchase->invoice_number}"
                ]);
            }

            // Update balance mutation if payment type changes
            if ($purchase->payment_type !== $request->payment_type) {
                if ($purchase->payment_type === 'cash') {
                    // Revert previous cash payment
                    $store = $purchase->store;
                    $balance = $store->balance;

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

                if ($request->payment_type === 'cash') {
                    // Add new cash payment
                    $store = $purchase->store;
                    $balance = $store->balance;

                    $previousBalance = $balance->cash_amount;
                    $currentBalance = $previousBalance - $request->final_amount;

                    BalanceMutation::create([
                        'store_id' => $store->id,
                        'type' => 'out',
                        'payment_method' => 'cash',
                        'amount' => $request->final_amount,
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

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating purchase order: ' . $e->getMessage());
        }
    }

    public function searchProducts(Request $request)
    {
        $query = Product::query()
            ->with(['units' => function($q) {
                $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
            }]);

        // Filter by store for non-admin users
        if (auth()->user()->role !== 'admin') {
            $query->where('store_id', auth()->user()->store_id);
        }

        // Search by name or code
        if ($request->has('term')) {
            $term = $request->term;
            $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('code', 'LIKE', "%{$term}%");
            });
        }

        $products = $query->take(10)->get();

        return response()->json([
            'results' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->code . ' - ' . $product->name,
                    'units' => $product->units
                ];
            })
        ]);
    }
}
