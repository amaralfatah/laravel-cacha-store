<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockTake;
use App\Models\StockHistory;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DB;
use Yajra\DataTables\DataTables;

class StockTakeController extends Controller
{
    public function index()
    {
        $stockTakes = StockTake::with('creator')
            ->latest()
            ->paginate(10);

        return view('stock-takes.index', compact('stockTakes'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->with(['productUnits.unit', 'category'])
            ->get();
        $categories = Category::where('is_active', true)->get();

        return view('stock-takes.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.actual_qty' => 'nullable|numeric|min:0',
        ]);

        // Validate at least one item has actual_qty
        $hasQuantity = false;
        foreach ($validated['items'] as $item) {
            if (isset($item['actual_qty']) && $item['actual_qty'] !== null && $item['actual_qty'] !== '') {
                $hasQuantity = true;
                break;
            }
        }

        if (!$hasQuantity) {
            throw ValidationException::withMessages([
                'items' => 'At least one item must have actual quantity filled'
            ]);
        }

        $this->validateUniqueUnits($validated['items']);

        try {
            DB::beginTransaction();

            $stockTake = StockTake::create([
                'date' => $validated['date'],
                'notes' => $validated['notes'],
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Filter hanya item yang memiliki actual_qty
            foreach ($validated['items'] as $item) {
                // Skip jika actual_qty kosong
                if (!isset($item['actual_qty']) || $item['actual_qty'] === '' || $item['actual_qty'] === null) {
                    continue;
                }

                // Get current stock from product_units table
                $productUnit = ProductUnit::where('product_id', $item['product_id'])
                    ->where('unit_id', $item['unit_id'])
                    ->first();

                $systemQty = $productUnit ? $productUnit->stock : 0;

                $stockTake->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'system_qty' => $systemQty,
                    'actual_qty' => $item['actual_qty'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('stock-takes.show', $stockTake)
                ->with('success', 'Stock take created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create stock take: ' . $e->getMessage());
        }
    }

    public function show(StockTake $stockTake)
    {
        $stockTake->load(['items.product', 'items.unit', 'creator']);
        return view('stock-takes.show', compact('stockTake'));
    }

    public function complete(StockTake $stockTake)
    {
        if ($stockTake->status !== 'draft') {
            return back()->with('error', 'Stock take already completed');
        }

        try {
            DB::beginTransaction();

            // Update product unit quantities and create stock histories
            foreach ($stockTake->items as $item) {
                $productUnit = ProductUnit::where('product_id', $item['product_id'])
                    ->where('unit_id', $item['unit_id'])
                    ->first();

                if (!$productUnit) {
                    throw new \Exception("Product unit combination not found");
                }

                // Update stock in product_units
                $oldStock = $productUnit->stock;
                $difference = $item->actual_qty - $item->system_qty;

                $productUnit->update([
                    'stock' => $item->actual_qty
                ]);

                // Create stock history record
                StockHistory::create([
                    'product_unit_id' => $productUnit->id,
                    'reference_type' => 'stock_takes',
                    'reference_id' => $stockTake->id,
                    'type' => $difference >= 0 ? 'adjustment' : 'adjustment',
                    'quantity' => abs($difference),
                    'remaining_stock' => $item->actual_qty,
                    'notes' => "Stock take adjustment from {$oldStock} to {$item->actual_qty}",
                ]);
            }

            $stockTake->update(['status' => 'completed']);

            DB::commit();

            return redirect()
                ->route('stock-takes.index')
                ->with('success', 'Stock take completed and inventory updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete stock take: ' . $e->getMessage());
        }
    }

    protected function validateUniqueUnits($items)
    {
        $combinations = [];
        foreach ($items as $item) {
            $key = $item['product_id'] . '-' . $item['unit_id'];
            if (in_array($key, $combinations)) {
                throw ValidationException::withMessages([
                    'items' => 'Duplicate product and unit combination found'
                ]);
            }
            $combinations[] = $key;
        }
    }

    public function data()
    {
        $stockTakes = StockTake::with(['items', 'creator']);

        return DataTables::of($stockTakes)
            ->editColumn('date', function($stockTake) {
                return $stockTake->date->format('Y-m-d');
            })
            ->editColumn('items_count', function($stockTake) {
                return $stockTake->items->count();
            })
            ->editColumn('status', function($stockTake) {
                $badgeClass = $stockTake->status === 'completed' ? 'success' : 'warning';
                return "<span class='badge bg-{$badgeClass}'>" . ucfirst($stockTake->status) . "</span>";
            })
            ->editColumn('creator_name', function($stockTake) {
                return $stockTake->creator->name ?? '-';
            })
            ->addColumn('action', function($stockTake) {
                return '<a href="'. route('stock-takes.show', $stockTake) .'" class="btn btn-sm btn-info">
                <i class="bi bi-eye"></i> View
            </a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}
