<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    protected $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

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
        $validator = Validator::make($request->all(), [
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_type' => 'required|in:cash,transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
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

        try {
            // Tentukan store_id
            $data = $request->all();
            $data['store_id'] = auth()->user()->role === 'admin'
                ? $request->store_id
                : auth()->user()->store_id;

            // Gunakan service untuk create purchase order
            $purchase = $this->purchaseOrderService->create($data);

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order created successfully');
        } catch (\Exception $e) {
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

            try {
                // Gunakan service untuk update status
                $this->purchaseOrderService->updateStatus($purchase, $request->status);

                return redirect()->route('purchases.show', $purchase)
                    ->with('success', 'Purchase order status updated successfully');
            } catch (\Exception $e) {
                return back()->with('error', 'Error updating purchase order: ' . $e->getMessage());
            }
        }

        // Jika update purchase order (edit)
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_type' => 'required|in:cash,transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
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

        try {
            // Gunakan service untuk update purchase order
            $this->purchaseOrderService->update($purchase, $request->all());

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order updated successfully');
        } catch (\Exception $e) {
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
