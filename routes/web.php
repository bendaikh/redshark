<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GlobalDashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdsCampaignController;
use App\Http\Controllers\AdsPlatformController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SourcingController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TestingProductController;

Route::get('/', function () {
	// Redirect guests to login
	if (!auth()->check()) {
		return redirect()->route('login');
	}

	// Redirect superadmin to admin dashboard
	if (auth()->user()->hasRole('superadmin')) {
		return redirect()->route('admin.dashboard');
	}

	// Redirect regular users to dashboard
	return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Serve storage files (workaround for Windows symlink issues)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    abort(404);
})->where('path', '.*');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Superadmin area
Route::middleware(['auth', 'role:superadmin'])
	->prefix('admin')
	->group(function () {
		// Admin dashboard (global overview)
		Route::get('/', [GlobalDashboardController::class, 'index'])->name('admin.dashboard');

		// Settings
		Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
		
		// Delivery Fees
		Route::post('/settings/delivery-fees', [SettingsController::class, 'storeDeliveryFee'])->name('admin.settings.delivery-fees.store');
		Route::put('/settings/delivery-fees/{deliveryFee}', [SettingsController::class, 'updateDeliveryFee'])->name('admin.settings.delivery-fees.update');
		Route::delete('/settings/delivery-fees/{deliveryFee}', [SettingsController::class, 'destroyDeliveryFee'])->name('admin.settings.delivery-fees.destroy');

		// Core resources (keep route names the same as views expect)
		Route::resource('countries', CountryController::class);
		Route::resource('products', ProductController::class);
		Route::get('products/{product}/statistics', [ProductController::class, 'statistics'])->name('products.statistics');
		Route::resource('categories', CategoryController::class);
		Route::resource('suppliers', SupplierController::class);
		Route::resource('invoices', InvoiceController::class);
		Route::resource('ads-campaigns', AdsCampaignController::class);
		Route::resource('ads-platforms', AdsPlatformController::class);
		Route::resource('sourcings', SourcingController::class);
		Route::post('sourcings/{sourcing}/validate', [SourcingController::class, 'validateSourcing'])->name('sourcings.validate');
		Route::resource('shipping-methods', ShippingMethodController::class);
		
		// Accounting resources
		Route::resource('expense-categories', ExpenseCategoryController::class);
		Route::resource('balances', BalanceController::class);
		Route::resource('expenses', ExpenseController::class);
		
		// Testing Products
		Route::resource('testing-products', TestingProductController::class);
	});

require __DIR__.'/auth.php';
