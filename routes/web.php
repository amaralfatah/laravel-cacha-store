<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockTakeController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Dashboard & Misc Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::resource('groups', GroupController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('taxes', TaxController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('units', UnitController::class);
    Route::resource('products', ProductController::class);
    Route::resource('products.units', ProductUnitController::class)->except(['index', 'show']);
    Route::resource('products.prices', ProductPriceController::class)->except(['index', 'show']);

    // Product Import Routes
    Route::prefix('products-import')->group(function () {
        Route::get('', [ProductImportController::class, 'showImportForm'])->name('products.import.form');
        Route::post('', [ProductImportController::class, 'import'])->name('products.import');
        Route::get('template', [ProductImportController::class, 'downloadTemplate'])->name('products.import.template');
    });

    // POS Routes
    Route::prefix('pos')->group(function () {
        Route::get('', [POSController::class, 'index'])->name('pos.index');
        Route::get('get-product', [POSController::class, 'getProduct'])->name('pos.get-product');
        Route::get('search-product', [POSController::class, 'searchProduct'])->name('pos.search-product');
        Route::post('', [POSController::class, 'store'])->name('pos.store');
        Route::get('invoice/{transaction}', [POSController::class, 'printInvoice'])->name('pos.print-invoice');
    });

    // Transaction Routes
    Route::resource('transactions', TransactionController::class)->only(['index']);
    Route::get('transactions/{transaction}/continue', [TransactionController::class, 'continue'])->name('transactions.continue');

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('', [ReportController::class, 'index'])->name('index');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('stock-movement', [ReportController::class, 'stockMovement'])->name('stock-movement');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        // Export Routes
        Route::get('export-sales', [ReportController::class, 'exportSales'])->name('export-sales');
        Route::get('export-inventory', [ReportController::class, 'exportInventory'])->name('export-inventory');
        Route::get('export-financial', [ReportController::class, 'exportFinancial'])->name('export-financial');
    });

    // Search Route
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // Stock Take Routes
    Route::prefix('stock-takes')->group(function () {
        Route::get('data', [StockTakeController::class, 'data'])->name('stock-takes.data');
        Route::get('get-products', [StockTakeController::class, 'getProducts'])->name('stock-takes.products');
        Route::patch('{stock_take}/complete', [StockTakeController::class, 'complete'])->name('stock-takes.complete');
        Route::resource('', StockTakeController::class);
    });

    // Stock Routes
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::resource('adjustments', StockAdjustmentController::class)->except(['show','edit', 'update', 'delete']);
        Route::get('adjustments/data', [StockAdjustmentController::class, 'data'])->name('adjustments.data');
        Route::resource('histories', StockHistoryController::class)->only(['index', 'show']);
    });

    // Store Routes
    Route::resource('stores', StoreController::class);
    Route::patch('stores/{store}/toggle-status', [StoreController::class, 'toggleStatus'])->name('stores.toggle-status');
});
