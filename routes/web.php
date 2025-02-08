<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        });

        Route::resource('users', UserController::class);
    });

    // Cashier Routes
    Route::middleware('role:cashier')->prefix('cashier')->group(function () {
        Route::get('/dashboard', function () {
            return view('cashier.dashboard');
        });
    });

    Route::resource('categories', CategoryController::class);

    Route::resource('taxes', TaxController::class);

    Route::resource('discounts', DiscountController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('customers', CustomerController::class);

    Route::resource('units', UnitController::class);

    Route::resource('products', ProductController::class);
    Route::put('products/{product}/update-price', [ProductController::class, 'updatePrice'])
        ->name('products.update-price');

    Route::resource('products.units', ProductUnitController::class);

    Route::resource('inventory', InventoryController::class);
    Route::get('check-low-stock', [InventoryController::class, 'checkLowStock'])->name('inventory.check-low-stock');

    Route::get('product-price', [ProductPriceController::class, 'index'])->name('product-price.index');
    Route::get('product-price/{product}/edit', [ProductPriceController::class, 'edit'])->name('product-price.edit');
    Route::put('product-price/{product}', [ProductPriceController::class, 'update'])->name('product-price.update');
    Route::post('product-price/{product}/price-tier', [ProductPriceController::class, 'storePriceTier'])->name('product-price.price-tier.store');
    Route::delete('product-price/price-tier/{priceTier}', [ProductPriceController::class, 'destroyPriceTier'])->name('product-price.price-tier.destroy');
});
