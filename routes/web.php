<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockAdjustmentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/items', [InventoryController::class, 'items'])->name('items');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/store', [InventoryController::class, 'store'])->name('store');
        Route::resource('categories', CategoryController::class);
        // Stock Adjustments
        Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])->name('stockAdjustments');
        Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stockAdjustments.create');
        Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('stockAdjustments.store');

        Route::get('/stock-history', [InventoryController::class, 'stockHistory'])->name('stockHistory');
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

    // Contacts
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/customers', [ContactsController::class, 'customers'])->name('customers');
        Route::get('/suppliers', [ContactsController::class, 'suppliers'])->name('suppliers');
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

require __DIR__.'/auth.php';
