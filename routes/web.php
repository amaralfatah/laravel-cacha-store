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
use App\Http\Controllers\User\StoreController as UserStoreController;
use App\Http\Controllers\User\StoreBalanceController as UserStoreBalanceController;
use App\Http\Controllers\StoreBalanceController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {

        Route::prefix('store')->name('store.')->group(function () {

            Route::get('/', [UserStoreController::class, 'show'])->name('show');
            Route::get('/edit', [UserStoreController::class, 'edit'])->name('edit');
            Route::put('/', [UserStoreController::class, 'update'])->name('update');

            Route::prefix('balance')->name('balance.')->group(function () {
                Route::get('/', [UserStoreBalanceController::class, 'show'])
                    ->name('show');
                Route::get('/history', [UserStoreBalanceController::class, 'history'])
                    ->name('history');
                Route::post('/adjustment', [UserStoreBalanceController::class, 'adjustment'])
                    ->name('adjustment');
            });
        });
    });

    // Dashboard & Misc Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

    // General Routes
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
    Route::get('products-import', [ProductImportController::class, 'showImportForm'])->name('products.import.form');
    Route::post('products-import', [ProductImportController::class, 'import'])->name('products.import');
    Route::get('products-import/template', [ProductImportController::class, 'downloadTemplate'])->name('products.import.template');

    // POS Routes
    Route::get('pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('pos/get-product', [POSController::class, 'getProduct'])->name('pos.get-product');
    Route::get('pos/search-product', [POSController::class, 'searchProduct'])->name('pos.search-product');
    Route::post('pos', [POSController::class, 'store'])->name('pos.store');
    Route::get('pos/invoice/{transaction}', [POSController::class, 'printInvoice'])->name('pos.print-invoice');

    // Transaction Routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}/continue', [TransactionController::class, 'continue'])->name('transactions.continue');

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'storeSales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'storeInventory'])->name('inventory');
        Route::get('/stock-movement', [ReportController::class, 'storeStockMovement'])->name('stock-movement');
        Route::get('/financial', [ReportController::class, 'storeFinancial'])->name('financial');

    });

    // Search Route
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // Stock Take Routes (Custom routes before resource)
    Route::get('stock-takes/data', [StockTakeController::class, 'data'])->name('stock-takes.data');
    Route::get('stock-takes/get-products', [StockTakeController::class, 'getProducts'])->name('stock-takes.products');
    Route::patch('stock-takes/{stock_take}/complete', [StockTakeController::class, 'complete'])->name('stock-takes.complete');

    // Resource route for stock-takes
    Route::resource('stock-takes', StockTakeController::class);

    // Stock Routes
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::resource('adjustments', StockAdjustmentController::class)->except(['show','edit', 'update', 'delete']);
        Route::get('adjustments/data', [StockAdjustmentController::class, 'data'])->name('adjustments.data');
        Route::resource('histories', StockHistoryController::class)->only(['index', 'show']);
    });

    // Store Routes
    Route::prefix('stores')->name('stores.')->group(function () {

        Route::prefix('balance')->name('balance.')->group(function () {
            Route::get('/{store}', [StoreBalanceController::class, 'show'])
                ->name('show');
            Route::get('/{store}/history', [StoreBalanceController::class, 'history'])
                ->name('history');
            Route::post('/{store}/adjustment', [StoreBalanceController::class, 'adjustment'])
                ->name('adjustment');
        });

        Route::resource('/', StoreController::class);
        Route::patch('/{store}/toggle-status', [StoreController::class, 'toggleStatus'])->name('toggle-status');
    });
});
