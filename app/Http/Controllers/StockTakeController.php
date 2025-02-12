<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockTake;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockTakeController extends Controller
{
    public function index()
    {
        $stockTakes = StockTake::with('creator')
            ->latest()
            ->paginate(10);

        return view('stock_takes.index', compact('stockTakes'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->with(['productUnits.unit', 'inventories', 'category'])
            ->get();
        $categories = Category::all();

        return view('stock_takes.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.actual_qty' => 'required|numeric|min:0',
        ]);

        $this->validateUniqueUnits($validated['items']);

        $stockTake = StockTake::create([
            'date' => $validated['date'],
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            // Get current stock for this product-unit combination
            $currentStock = Inventory::where('product_id', $item['product_id'])
                ->where('unit_id', $item['unit_id'])
                ->value('quantity') ?? 0;

            $stockTake->items()->create([
                'product_id' => $item['product_id'],
                'unit_id' => $item['unit_id'],
                'system_qty' => $currentStock,
                'actual_qty' => $item['actual_qty'],
            ]);
        }

        return redirect()
            ->route('stock-takes.show', $stockTake)
            ->with('success', 'Stock take created successfully');
    }

    public function show(StockTake $stockTake)
    {
        $stockTake->load(['items.product', 'creator']);
        return view('stock_takes.show', compact('stockTake'));
    }

    public function complete(StockTake $stockTake)
    {
        if ($stockTake->status !== 'draft') {
            return back()->with('error', 'Stock take already completed');
        }

        // Update actual inventory quantities
        foreach ($stockTake->items as $item) {
            Inventory::updateOrCreate(
                [
                    'product_id' => $item->product_id,
                    'unit_id' => $item->unit_id
                ],
                [
                    'quantity' => $item->actual_qty,
                    'min_stock' => 0 // Set default min_stock
                ]
            );
        }

        $stockTake->update(['status' => 'completed']);

        return redirect()
            ->route('stock-takes.index')
            ->with('success', 'Stock take completed and inventory updated');
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
}
