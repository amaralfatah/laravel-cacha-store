<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\BalanceMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $sales = Transaction::with(['customer', 'cashier', 'items.product', 'items.unit'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'success')
            ->orderBy('invoice_date', 'desc')
            ->get();

        $summary = [
            'total_sales' => $sales->sum('final_amount'),
            'total_transactions' => $sales->count(),
            'average_transaction' => $sales->count() > 0 ? $sales->sum('final_amount') / $sales->count() : 0,
            'total_items' => $sales->sum(function($sale) {
                return $sale->items->sum('quantity');
            }),
        ];

        return view('reports.sales', compact('sales', 'summary', 'startDate', 'endDate'));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(['category', 'productUnits.unit'])
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                $totalStock = $product->productUnits->sum('stock');
                $lowStock = $product->productUnits->filter(function ($unit) {
                        return $unit->stock <= $unit->min_stock;
                    })->count() > 0;

                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'total_stock' => $totalStock,
                    'low_stock' => $lowStock,
                    'units' => $product->productUnits
                ];
            });

        return view('reports.inventory', compact('products'));
    }

    public function stockMovement(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id'
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = StockHistory::with(['productUnit.product', 'productUnit.unit'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->product_id) {
            $query->whereHas('productUnit', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        $products = Product::where('is_active', true)->get();

        return view('reports.stock-movement', compact('movements', 'products', 'startDate', 'endDate'));
    }

    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $mutations = BalanceMutation::with('createdBy')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_in' => $mutations->where('type', 'in')->sum('amount'),
            'total_out' => $mutations->where('type', 'out')->sum('amount'),
            'net_amount' => $mutations->where('type', 'in')->sum('amount') - $mutations->where('type', 'out')->sum('amount'),
            'current_balance' => DB::table('store_balances')->first()->amount ?? 0,
        ];

        return view('reports.financial', compact('mutations', 'summary', 'startDate', 'endDate'));
    }

    public function exportSales(Request $request)
    {
        // Implementation for exporting sales report to Excel/PDF
    }

    public function exportInventory(Request $request)
    {
        // Implementation for exporting inventory report to Excel/PDF
    }

    public function exportFinancial(Request $request)
    {
        // Implementation for exporting financial report to Excel/PDF
    }
}
