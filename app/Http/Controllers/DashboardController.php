<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransactionItem;
use App\Models\ProductUnit;
use App\Models\StockHistory;
use App\Models\BalanceMutation;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected function getStoreId()
    {
        $user = Auth::user();
        return $user->role === 'admin' ? null : $user->store_id;
    }

    public function index()
    {
        $storeId = $this->getStoreId();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $yesterday = Carbon::yesterday();
        $lastMonth = Carbon::now()->subMonth();
        $startOfLastMonth = $lastMonth->copy()->startOfMonth();
        $endOfLastMonth = $lastMonth->copy()->endOfMonth();

        // Query basis berdasarkan role
        $query = Transaction::query();
        $productQuery = Product::query();
        $stockQuery = ProductUnit::query()->where('stock', '<', DB::raw('min_stock'));

        if ($storeId) {
            $query->where('transactions.store_id', $storeId);
            $productQuery->where('products.store_id', $storeId);
            $stockQuery->where('product_units.store_id', $storeId);
        }

        // Statistik Penjualan Hari Ini
        $todaySales = $query->clone()
            ->whereDate('transactions.created_at', $today)
            ->where('transactions.status', 'success')
            ->sum('transactions.final_amount');

        $todayTransactions = $query->clone()
            ->whereDate('transactions.created_at', $today)
            ->where('transactions.status', 'success')
            ->count();

        // Perbandingan dengan hari sebelumnya
        $yesterdaySales = $query->clone()
            ->whereDate('transactions.created_at', $yesterday)
            ->where('transactions.status', 'success')
            ->sum('transactions.final_amount');

        $salesGrowth = $yesterdaySales > 0
            ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100
            : ($todaySales > 0 ? 100 : 0);

        // Statistik Bulanan
        $monthlySales = $query->clone()
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
            ->where('transactions.status', 'success')
            ->sum('transactions.final_amount');

        $lastMonthSales = $query->clone()
            ->whereBetween('transactions.created_at', [$startOfLastMonth, $endOfLastMonth])
            ->where('transactions.status', 'success')
            ->sum('transactions.final_amount');

        $monthlyGrowth = $lastMonthSales > 0
            ? (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100
            : ($monthlySales > 0 ? 100 : 0);

        // Produk Terlaris
        $topProducts = TransactionItem::select(
            'products.name',
            'products.id as product_id',
            'units.name as unit_name',
            DB::raw('SUM(transaction_items.quantity) as total_quantity'),
            DB::raw('SUM(transaction_items.subtotal) as total_sales')
        )
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('units', 'transaction_items.unit_id', '=', 'units.id')
            ->where('transactions.status', 'success')
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth]);

        if ($storeId) {
            $topProducts->where('transactions.store_id', $storeId);
        }

        $topProducts = $topProducts->groupBy('products.id', 'products.name', 'units.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Stok Rendah - FIX: Specify the table name for store_id to avoid ambiguity
        $lowStockProducts = $stockQuery->select(
            'product_units.id',
            'products.name as product_name',
            'units.name as unit_name',
            'product_units.stock',
            'product_units.min_stock'
        )
            ->join('products', 'product_units.product_id', '=', 'products.id')
            ->join('units', 'product_units.unit_id', '=', 'units.id')
            ->orderBy('products.name')
            ->limit(10)
            ->get();

        // Grafik Penjualan
        $salesChart = $query->clone()
            ->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transactions.final_amount) as total_sales')
            )
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
            ->where('transactions.status', 'success')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->total_sales;
            });

        // Inisialisasi array grafik dengan semua tanggal dalam bulan
        $allDates = [];
        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth)) {
            $dateString = $currentDate->format('Y-m-d');
            $allDates[$dateString] = $salesChart[$dateString] ?? 0;
            $currentDate->addDay();
        }

        // Data store untuk admin
        $stores = [];
        if (Auth::user()->role === 'admin') {
            $stores = Store::where('is_active', true)
                ->withCount(['transactions as transaction_count' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
                        ->where('transactions.status', 'success');
                }])
                ->withSum(['transactions as sales_total' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
                        ->where('transactions.status', 'success');
                }], 'final_amount')
                ->orderByDesc('sales_total')
                ->limit(5)
                ->get();
        }

        // Kategori teratas
        $topCategories = TransactionItem::select(
            'categories.name',
            DB::raw('COUNT(transaction_items.id) as total_items'),
            DB::raw('SUM(transaction_items.subtotal) as total_sales')
        )
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('transactions.status', 'success')
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth]);

        if ($storeId) {
            $topCategories->where('transactions.store_id', $storeId);
        }

        $topCategories = $topCategories->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Total produk aktif
        $activeProducts = $productQuery->where('products.is_active', true)->count();

        return view('dashboard.index', compact(
            'todaySales',
            'todayTransactions',
            'salesGrowth',
            'monthlySales',
            'monthlyGrowth',
            'topProducts',
            'lowStockProducts',
            'allDates',
            'stores',
            'topCategories',
            'activeProducts'
        ));
    }

    // Endpoint untuk data grafik
    public function getChartData()
    {
        $storeId = $this->getStoreId();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Query basis berdasarkan role
        $query = Transaction::query();

        if ($storeId) {
            $query->where('transactions.store_id', $storeId);
        }

        // Data penjualan harian
        $dailySales = $query->clone()
            ->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transactions.final_amount) as total_sales'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
            ->where('transactions.status', 'success')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data metode pembayaran
        $paymentMethods = $query->clone()
            ->select(
                'transactions.payment_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(transactions.final_amount) as total_amount')
            )
            ->where('transactions.status', 'success')
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('transactions.payment_type')
            ->get();

        return response()->json([
            'daily_sales' => $dailySales,
            'payment_methods' => $paymentMethods,
        ]);
    }
}
