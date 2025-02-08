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
        // Get current date
        $today = Carbon::now()->toDateString();
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        // Get today's sales
        $todaySales = Transaction::whereDate('created_at', $today)
            ->where('status', 'success')
            ->sum('final_amount');

        $yesterdaySales = Transaction::whereDate('created_at', Carbon::yesterday())
            ->where('status', 'success')
            ->sum('final_amount');

        $salesPercentage = $yesterdaySales != 0 ?
            round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 2) :
            100;

        // Calculate total profit
        $totalProfit = DB::table('transactions as t')
            ->join('transaction_items as ti', 't.id', '=', 'ti.transaction_id')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->where('t.status', 'success')
            ->whereYear('t.created_at', $currentYear)
            ->select(DB::raw('SUM(ti.subtotal - (ti.quantity * p.base_price)) as total_profit'))
            ->value('total_profit');

        // Calculate monthly revenue
        $monthlyRevenue = Transaction::where('status', 'success')
            ->whereYear('created_at', $currentYear)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(final_amount) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Calculate year-over-year growth
        $currentYearRevenue = Transaction::where('status', 'success')
            ->whereYear('created_at', $currentYear)
            ->sum('final_amount');

        $previousYearRevenue = Transaction::where('status', 'success')
            ->whereYear('created_at', $previousYear)
            ->sum('final_amount');

        $companyGrowth = $previousYearRevenue != 0 ?
            round((($currentYearRevenue - $previousYearRevenue) / $previousYearRevenue) * 100, 2) :
            100;

        // Get order statistics by category
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
            ->limit(4)
            ->get();

        // Get recent transactions with customer details
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
        $currentWeekExpenses = Transaction::where('status', 'success')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('final_amount');

        $lastWeekExpenses = Transaction::where('status', 'success')
            ->whereBetween('created_at', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])
            ->sum('final_amount');

        $expenseDifference = $currentWeekExpenses - $lastWeekExpenses;

        // Get top selling products
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


        // Get total payments for the current period (e.g., month or week)
        $totalPayments = Transaction::where('status', 'success')
            ->sum('final_amount');

        // Get total transactions for the current period (e.g., month or week)
        $totalTransactions = Transaction::where('status', 'success')
            ->count();

        // Calculate the percentage change in payments compared to the previous period
        $previousPayments = Transaction::where('status', 'success')
            ->whereYear('created_at', $previousYear)
            ->sum('final_amount');
        $paymentChangePercentage = $previousPayments != 0
            ? round((($totalPayments - $previousPayments) / $previousPayments) * 100, 2)
            : 100;

        // Calculate the percentage change in transactions compared to the previous period
        $previousTransactions = Transaction::where('status', 'success')
            ->whereYear('created_at', $previousYear)
            ->count();
        $transactionChangePercentage = $previousTransactions != 0
            ? round((($totalTransactions - $previousTransactions) / $previousTransactions) * 100, 2)
            : 100;

        // Profile Report for Year 2021 (or any other specific year)
        $profileReportRevenue = Transaction::whereYear('created_at', 2021)
            ->sum('final_amount');
        $previousProfileReportRevenue = Transaction::whereYear('created_at', 2020)
            ->sum('final_amount');
        $profileReportChangePercentage = $previousProfileReportRevenue != 0
            ? round((($profileReportRevenue - $previousProfileReportRevenue) / $previousProfileReportRevenue) * 100, 2)
            : 100;


        $totalOrders = Transaction::where('status', 'success')->count();

        // Order Statistics by Category
        $orderStats = DB::table('transaction_items')
            ->select(DB::raw('categories.name as category'), DB::raw('COUNT(transaction_items.id) as total_orders'), DB::raw('SUM(transaction_items.quantity) as total_quantity'))
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->get();



        // Total Income: Sum of all final amounts for successful transactions
        $totalIncome = Transaction::where('status', 'success')
            ->sum('final_amount');

        // Expenses this week: Sum of all transactions in the current week
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $expensesThisWeek = Transaction::where('status', 'success')
            ->whereBetween('invoice_date', [$startOfWeek, $endOfWeek])
            ->sum('final_amount');

        // Expenses last week: Sum of all transactions in the previous week
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $expensesLastWeek = Transaction::where('status', 'success')
            ->whereBetween('invoice_date', [$startOfLastWeek, $endOfLastWeek])
            ->sum('final_amount');

        // Calculate weekly difference
        $expenseDifference = $expensesThisWeek - $expensesLastWeek;
        $expenseComparison = $expenseDifference < 0 ? "less" : "more";
        $expenseDifferencePercentage = $expensesLastWeek != 0 ? round(($expenseDifference / $expensesLastWeek) * 100, 2) : 0;

        // Calculate total profit
        $totalProfit = $totalIncome - $expensesThisWeek;

        // Prepare data for chart (can use fake data or based on real data)
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
            'chartData',
        ));
    }
}
