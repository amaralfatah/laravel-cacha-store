<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductUnit;
use App\Models\StockHistory;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Overview Statistics
        $overview = [
            'total_products' => ProductUnit::count(),
            'low_stock_count' => ProductUnit::where('stock', '<=', 10)->count(),
            'out_of_stock_count' => ProductUnit::where('stock', '<=', 0)->count(),
            'today_movements' => StockHistory::whereDate('created_at', Carbon::today())
                ->select('type', DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray()
        ];

        // Query for stock history
        $query = StockHistory::with(['productUnit.product.category', 'productUnit.unit']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->whereHas('productUnit', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('productUnit.product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $histories = $query->latest()->paginate(10);
        $products = Product::where('is_active', true)->get();
        $categories = Category::all();

        // Get low stock products
        $lowStockProducts = ProductUnit::with(['product', 'unit'])
            ->where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->take(5)
            ->get();

        return view('stock.histories.index', compact(
            'histories',
            'products',
            'categories',
            'overview',
            'lowStockProducts'
        ));
    }

    public function show(StockHistory $history)
    {
        $history->load(['productUnit.product.category', 'productUnit.unit']);
        return view('stock.histories.show', compact('history'));
    }
}
