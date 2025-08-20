<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\VendorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// POS
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [POSController::class, 'index'])->name('index');
    Route::get('/checkout', [POSController::class, 'checkoutPage'])->name('checkout.page');
    Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout');
    Route::post('/confirm/{id}', [POSController::class, 'confirmOrder'])->name('confirm');
    Route::get('/history', [POSController::class, 'orderHistory'])->name('history');
    Route::get('/order/{id}', [POSController::class, 'show'])->name('order.details');
});

// Inventory Management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('App/Dashboard');
    })->name('dashboard');

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/items', [InventoryController::class, 'items'])->name('items');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/store', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}/pos-availability', [InventoryController::class, 'updatePOSAvailability'])->name('updatePOSAvailability');

        // Categories
        Route::resource('categories', CategoryController::class);

        // Stock Adjustments
        Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])->name('stockAdjustments');
        Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stockAdjustments.create');
        Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('stockAdjustments.store');
        Route::get('/stock-history', [InventoryController::class, 'stockHistory'])->name('stockHistory');
        Route::get('/stock-movements', [InventoryController::class, 'stockMovements'])->name('stockMovements');

        // This should be the last route in this group
        Route::get('/items/{id}', [InventoryController::class, 'show'])->name('show')->where('id', '[0-9]+');
    });

    // Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::resource('orders', SalesOrderController::class);
    });
//    Route::prefix('sales')->name('sales.')->group(function () {
//        Route::get('/orders', [SalesController::class, 'orders'])->name('orders');
//        Route::get('/invoices', [SalesController::class, 'invoices'])->name('invoices');
//        Route::get('/payments', [SalesController::class, 'payments'])->name('payments');
//    });

    // Purchases
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/orders', [PurchasesController::class, 'orders'])->name('orders');
        Route::get('/bills', [PurchasesController::class, 'bills'])->name('bills');
        Route::get('/payments', [PurchasesController::class, 'payments'])->name('payments');
    });

    // Suppliers
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SuppliersController::class, 'index'])->name('index');
        Route::get('/create', [SuppliersController::class, 'create'])->name('create');
        Route::post('/', [SuppliersController::class, 'store'])->name('store');
        Route::get('/{supplier}/edit', [SuppliersController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SuppliersController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SuppliersController::class, 'destroy'])->name('destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/inventory-valuation', [ReportsController::class, 'inventoryValuation'])->name('inventoryValuation');
        Route::get('/sales', [ReportsController::class, 'sales'])->name('sales');
        Route::get('/purchases', [ReportsController::class, 'purchases'])->name('purchases');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/company', [SettingsController::class, 'company'])->name('company');
        Route::get('/users-permissions', [SettingsController::class, 'usersPermissions'])->name('usersPermissions');
        Route::get('/billing', [SettingsController::class, 'billing'])->name('billing');
    });
});

// Vendors
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/purchases', [VendorController::class, 'purchaseHistory'])->name('purchases.index');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::post('/{vendor}/purchases', [VendorController::class, 'storePurchaseOrder'])->name('purchases.store');
        Route::get('/purchases/{purchaseOrder}', [VendorController::class, 'showPurchaseOrder'])->name('purchases.show');
    });
});

require __DIR__.'/auth.php';
