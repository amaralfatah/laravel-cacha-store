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
            ->when(auth()->user()->role !== 'admin', function ($query) {
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
            $products = Product::with([
                'units' => function ($q) {
                    $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                }
            ])->get();
        } else {
            // User biasa hanya bisa lihat data store-nya
            $suppliers = Supplier::where('store_id', auth()->user()->store_id)->get();
            $products = Product::where('store_id', auth()->user()->store_id)
                ->with([
                    'units' => function ($q) {
                        $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                    }
                ])
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
            $products = Product::with([
                'units' => function ($q) {
                    $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                }
            ])->get();
            $stores = Store::all();
        } else {
            $suppliers = Supplier::where('store_id', auth()->user()->store_id)->get();
            $products = Product::where('store_id', auth()->user()->store_id)
                ->with([
                    'units' => function ($q) {
                        $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                    }
                ])
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
            ->with(['productUnits.unit'])
            ->select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.store_id'
            ]);

        // Filter berdasarkan store untuk non-admin
        if (auth()->user()->role !== 'admin') {
            $query->where('products.store_id', auth()->user()->store_id);
        }

        // Pencarian global
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.barcode', 'like', "%{$search}%");
            });
        }

        // Pencarian per kolom
        if ($request->filled('columns')) {
            foreach ($request->input('columns') as $column) {
                if ($column['searchable'] && !empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];
                    $columnName = $column['data'];

                    if (in_array($columnName, ['name', 'barcode'])) {
                        $query->where("products.{$columnName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        // Sorting
        if ($request->filled('order')) {
            foreach ($request->input('order') as $order) {
                $columnIndex = $order['column'];
                $columnName = $request->input("columns.{$columnIndex}.data");
                $direction = $order['dir'];

                if ($columnName && in_array($columnName, ['name', 'barcode'])) {
                    $query->orderBy("products.{$columnName}", $direction);
                }
            }
        } else {
            $query->orderBy('products.name', 'asc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);

        $total = $query->count();
        $products = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'stock' => $product->productUnits->sum('stock'),
                    'units' => $product->productUnits->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'name' => $unit->unit->name,
                            'purchase_price' => $unit->purchase_price,
                            'stock' => $unit->stock
                        ];
                    })
                ];
            })
        ]);
    }

    public function getProducts()
    {
        $query = Product::query()
            ->with([
                'units' => function ($q) {
                    $q->select('units.id', 'units.name', 'product_units.purchase_price', 'product_units.stock');
                }
            ])
            ->select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.store_id'
            ]);

        // Filter berdasarkan store untuk non-admin
        if (auth()->user()->role !== 'admin') {
            $query->where('products.store_id', auth()->user()->store_id);
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'units' => $product->units->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'purchase_price' => $unit->purchase_price,
                        'stock' => $unit->stock,
                    ];
                })
            ];
        });

        return response()->json($products);
    }
}
