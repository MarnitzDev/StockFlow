<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\OrganizationController;
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
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/update-stock', [ProductController::class, 'updateStock'])->name('updateStock');
        Route::get('/low-stock-alert', [ProductController::class, 'lowStockAlert'])->name('lowStockAlert');
    });

    Route::resource('contacts', ContactController::class);
    Route::get('/inventory-report', [InventoryReportController::class, 'index'])->name('inventory.report');

    Route::prefix('organization')->name('organization.')->middleware(['auth'])->group(function () {
        Route::get('/profile', [OrganizationController::class, 'profile'])->name('profile');
        Route::get('/branding', [OrganizationController::class, 'branding'])->name('branding');
        Route::get('/locations', [OrganizationController::class, 'locations'])->name('locations');
        Route::get('/currency', [OrganizationController::class, 'currency'])->name('currency');
    });

});

require __DIR__.'/auth.php';
