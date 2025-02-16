<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\StockAdjustment;
use App\Models\Store;
use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\DataTables;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $query = StockAdjustment::with(['productUnit.product', 'productUnit.unit', 'creator', 'store'])
            ->latest();

        if (auth()->user()->role !== 'admin') {
            $query->where('store_id', auth()->user()->store_id);
        }

        $adjustments = $query->get();
        return view('stock.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $productsQuery = Product::with(['productUnits.unit'])
            ->where('is_active', true);

        if (auth()->user()->role !== 'admin') {
            $productsQuery->where('store_id', auth()->user()->store_id);
        }

        $products = $productsQuery->get();
        $stores = auth()->user()->role === 'admin' ? Store::all() : [];

        return view('stock.adjustments.create', compact('products', 'stores'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'product_unit_id' => 'required|exists:product_units,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $validated = $request->validate($validationRules);

        try {
            DB::beginTransaction();

            $productUnit = ProductUnit::findOrFail($validated['product_unit_id']);

            // Check if user has access to this product's store
            if (auth()->user()->role !== 'admin' && $productUnit->product->store_id !== auth()->user()->store_id) {
                throw new \Exception("Unauthorized access to product");
            }

            if ($validated['type'] === 'out' && $productUnit->stock < $validated['quantity']) {
                throw new \Exception("Insufficient stock. Current stock: {$productUnit->stock}");
            }

            if ($validated['type'] === 'in') {
                $productUnit->increment('stock', $validated['quantity']);
            } else {
                $productUnit->decrement('stock', $validated['quantity']);
            }

            StockAdjustment::create([
                'product_unit_id' => $validated['product_unit_id'],
                'store_id' => auth()->user()->role === 'admin' ? $request->store_id : auth()->user()->store_id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id()
            ]);

            DB::commit();

            // Check if redirect_back exists in request
            if ($request->has('redirect_back')) {
                return back()->with('success', 'Stock adjustment completed successfully');
            }

            return redirect()->route('stock.adjustments.index')
                ->with('success', 'Stock adjustment completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function data()
    {
        $query = StockAdjustment::with(['productUnit.product', 'productUnit.unit', 'creator', 'store']);

        if (auth()->user()->role !== 'admin') {
            $query->where('store_id', auth()->user()->store_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('product_name', function ($adjustment) {
                return $adjustment->productUnit->product->name ?? '-';
            })
            ->addColumn('unit_name', function ($adjustment) {
                return $adjustment->productUnit->unit->name ?? '-';
            })
            ->addColumn('store_name', function ($adjustment) {
                return $adjustment->store->name ?? '-';
            })
            ->editColumn('created_at', function ($adjustment) {
                return $adjustment->created_at->format('Y-m-d H:i');
            })
            ->editColumn('type', function ($adjustment) {
                $badgeClass = $adjustment->type === 'in' ? 'success' : 'danger';
                return "<span class='badge bg-{$badgeClass}'>" . ucfirst($adjustment->type) . "</span>";
            })
            ->editColumn('quantity', function ($adjustment) {
                return number_format($adjustment->quantity, 2);
            })
            ->addColumn('creator_name', function ($adjustment) {
                return $adjustment->creator->name ?? '-';
            })
            ->rawColumns(['type'])
            ->make(true);
    }
}
