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
use Yajra\DataTables\DataTables;

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

        if ($request->ajax()) {
            $query = StockHistory::with([
                'productUnit.product.category',
                'productUnit.unit'
            ]);

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

            return DataTables::of($query)
                ->addColumn('date', function ($history) {
                    return $history->created_at->format('Y-m-d H:i');
                })
                ->addColumn('product_name', function ($history) {
                    return $history->productUnit->product->name;
                })
                ->addColumn('category_name', function ($history) {
                    return $history->productUnit->product->category->name;
                })
                ->addColumn('unit_name', function ($history) {
                    return $history->productUnit->unit->name;
                })
                ->addColumn('type_badge', function ($history) {
                    $badges = [
                        'in' => '<span class="badge bg-success">Stock In</span>',
                        'out' => '<span class="badge bg-danger">Stock Out</span>',
                        'adjustment' => '<span class="badge bg-warning">Adjustment</span>'
                    ];
                    return $badges[$history->type] ?? ucfirst($history->type);
                })
                ->addColumn('quantity_formatted', function ($history) {
                    return number_format($history->quantity, 2);
                })
                ->addColumn('remaining_formatted', function ($history) {
                    return number_format($history->remaining_stock, 2);
                })
                ->addColumn('source', function ($history) {
                    return ucfirst(str_replace('_', ' ', $history->reference_type));
                })
                ->rawColumns(['type_badge'])
                ->make(true);
        }

        // Get low stock products
        $lowStockProducts = ProductUnit::with(['product', 'unit'])
            ->where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->take(5)
            ->get();

        $products = Product::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('stock.histories.index', compact(
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
