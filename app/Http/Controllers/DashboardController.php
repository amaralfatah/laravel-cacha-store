<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Initialize date variables
        $today = Carbon::now()->toDateString();
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Base query for successful transactions
        $successfulTransactions = Transaction::where('status', 'success');

        // Daily sales calculations
        $todaySales = clone $successfulTransactions;
        $todaySales = $todaySales->whereDate('created_at', $today)->sum('final_amount');

        $yesterdaySales = clone $successfulTransactions;
        $yesterdaySales = $yesterdaySales->whereDate('created_at', Carbon::yesterday())->sum('final_amount');

        $salesPercentage = $yesterdaySales != 0 ?
            round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 2) : 100;

        // Yearly calculations
        $currentYearTransactions = clone $successfulTransactions;
        $currentYearRevenue = $currentYearTransactions->whereYear('created_at', $currentYear)->sum('final_amount');

        $previousYearTransactions = clone $successfulTransactions;
        $previousYearRevenue = $previousYearTransactions->whereYear('created_at', $previousYear)->sum('final_amount');

        $companyGrowth = $previousYearRevenue != 0 ?
            round((($currentYearRevenue - $previousYearRevenue) / $previousYearRevenue) * 100, 2) : 100;

        // Total profit calculation
        $totalProfit = DB::table('transactions as t')
            ->join('transaction_items as ti', 't.id', '=', 'ti.transaction_id')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->where('t.status', 'success')
            ->whereYear('t.created_at', $currentYear)
            ->select(DB::raw('SUM(ti.subtotal - (ti.quantity * p.base_price)) as total_profit'))
            ->value('total_profit');

        // Monthly revenue data
        $monthlyRevenue = clone $successfulTransactions;
        $monthlyRevenue = $monthlyRevenue->whereYear('created_at', $currentYear)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(final_amount) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Order statistics by category
        $orderStats = DB::table('transaction_items as ti')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('transactions as t', 'ti.transaction_id', '=', 't.id')
            ->where('t.status', 'success')
            ->select(
                'c.name as category',
                DB::raw('COUNT(DISTINCT ti.transaction_id) as total_orders'),
                DB::raw('SUM(ti.quantity) as total_quantity')
            )
            ->groupBy('c.id', 'c.name')
            ->orderBy('total_orders', 'desc')
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with(['customer'])
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->final_amount,
                    'type' => $transaction->payment_type,
                    'customer' => $transaction->customer ? $transaction->customer->name : 'Guest',
                    'date' => $transaction->created_at->format('Y-m-d H:i:s')
                ];
            });

        // Weekly expenses calculations
        $currentWeekExpenses = clone $successfulTransactions;
        $currentWeekExpenses = $currentWeekExpenses
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->sum('final_amount');

        $lastWeekExpenses = clone $successfulTransactions;
        $lastWeekExpenses = $lastWeekExpenses
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->sum('final_amount');

        $expenseDifference = $currentWeekExpenses - $lastWeekExpenses;
        $expenseComparison = $expenseDifference < 0 ? "less" : "more";
        $expenseDifferencePercentage = $lastWeekExpenses != 0 ?
            round(($expenseDifference / $lastWeekExpenses) * 100, 2) : 0;

        // Top selling products
        $topProducts = DB::table('transaction_items as ti')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->join('transactions as t', 'ti.transaction_id', '=', 't.id')
            ->where('t.status', 'success')
            ->whereMonth('t.created_at', Carbon::now()->month)
            ->select(
                'p.name',
                DB::raw('SUM(ti.quantity) as total_quantity'),
                DB::raw('SUM(ti.subtotal) as total_sales')
            )
            ->groupBy('p.id', 'p.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Total calculations
        $totalPayments = clone $successfulTransactions;
        $totalPayments = $totalPayments->sum('final_amount');

        $totalTransactions = clone $successfulTransactions;
        $totalTransactions = $totalTransactions->count();

        $totalOrders = $totalTransactions; // Since they represent the same thing

        // Change percentage calculations
        $paymentChangePercentage = $previousYearRevenue != 0 ?
            round((($totalPayments - $previousYearRevenue) / $previousYearRevenue) * 100, 2) : 100;

        $previousTransactionsCount = clone $successfulTransactions;
        $previousTransactionsCount = $previousTransactionsCount
            ->whereYear('created_at', $previousYear)
            ->count();

        $transactionChangePercentage = $previousTransactionsCount != 0 ?
            round((($totalTransactions - $previousTransactionsCount) / $previousTransactionsCount) * 100, 2) : 100;

        // Profile report calculations (2021 specific)
        $profileReportRevenue = clone $successfulTransactions;
        $profileReportRevenue = $profileReportRevenue->whereYear('created_at', 2021)->sum('final_amount');

        $previousProfileRevenue = clone $successfulTransactions;
        $previousProfileRevenue = $previousProfileRevenue->whereYear('created_at', 2020)->sum('final_amount');

        $profileReportChangePercentage = $previousProfileRevenue != 0 ?
            round((($profileReportRevenue - $previousProfileRevenue) / $previousProfileRevenue) * 100, 2) : 100;

        // Income and expense summary
        $totalIncome = $totalPayments; // They represent the same value
        $expensesThisWeek = $currentWeekExpenses;
        $expensesLastWeek = $lastWeekExpenses;

        // Chart data
        $chartData = [
            'income' => $totalIncome,
            'expenses' => $expensesThisWeek,
            'profit' => $totalProfit
        ];

        return view('dashboard.index', compact(
            'todaySales',
            'salesPercentage',
            'totalProfit',
            'monthlyRevenue',
            'currentYearRevenue',
            'previousYearRevenue',
            'companyGrowth',
            'totalOrders',
            'orderStats',
            'recentTransactions',
            'currentWeekExpenses',
            'expenseDifference',
            'topProducts',
            'totalPayments',
            'paymentChangePercentage',
            'totalTransactions',
            'transactionChangePercentage',
            'profileReportRevenue',
            'profileReportChangePercentage',
            'totalIncome',
            'expensesThisWeek',
            'expensesLastWeek',
            'expenseDifference',
            'expenseComparison',
            'expenseDifferencePercentage',
            'totalProfit',
            'chartData'
        ));
    }
}
