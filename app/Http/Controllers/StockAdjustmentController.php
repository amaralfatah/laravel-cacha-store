<?php

// app/Http/Controllers/Stock/StockAdjustmentController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\StockAdjustment;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\DataTables;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with(['productUnit.product', 'productUnit.unit', 'creator'])
            ->latest()
            ->paginate(10);

        return view('stock.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::with(['productUnits.unit'])
            ->where('is_active', true)
            ->get();

        return view('stock.adjustments.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_unit_id' => 'required|exists:product_units,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $productUnit = ProductUnit::findOrFail($validated['product_unit_id']);

            // Check stock if type is 'out'
            if ($validated['type'] === 'out' && $productUnit->stock < $validated['quantity']) {
                throw new \Exception("Insufficient stock. Current stock: {$productUnit->stock}");
            }

            // Update stock
            if ($validated['type'] === 'in') {
                $productUnit->increment('stock', $validated['quantity']);
            } else {
                $productUnit->decrement('stock', $validated['quantity']);
            }

            // Create adjustment record
            StockAdjustment::create([
                'product_unit_id' => $validated['product_unit_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('stock.adjustments.index')
                ->with('success', 'Stock adjustment completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function data()
    {
        $adjustments = StockAdjustment::with(['productUnit.product', 'productUnit.unit', 'creator']);

        return DataTables::of($adjustments)
            ->addIndexColumn()
            ->addColumn('product_name', function($adjustment) {
                return $adjustment->productUnit->product->name ?? '-';
            })
            ->addColumn('unit_name', function($adjustment) {
                return $adjustment->productUnit->unit->name ?? '-';
            })
            ->editColumn('created_at', function($adjustment) {
                return $adjustment->created_at->format('Y-m-d H:i');
            })
            ->editColumn('type', function($adjustment) {
                $badgeClass = $adjustment->type === 'in' ? 'success' : 'danger';
                return "<span class='badge bg-{$badgeClass}'>" . ucfirst($adjustment->type) . "</span>";
            })
            ->editColumn('quantity', function($adjustment) {
                return number_format($adjustment->quantity, 2);
            })
            ->addColumn('creator_name', function($adjustment) {
                return $adjustment->creator->name ?? '-';
            })
            ->rawColumns(['type'])
            ->make(true);
    }
}
