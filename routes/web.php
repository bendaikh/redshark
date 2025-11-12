<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalDashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdsCampaignController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
		Route::get('/settings', function () {
			$countries = \App\Models\Country::orderBy('name')->paginate(10);
			return view('admin.settings', compact('countries'));
		})->name('admin.settings');

		// Core resources (keep route names the same as views expect)
		Route::resource('countries', CountryController::class);
		Route::resource('products', ProductController::class);
		Route::resource('categories', CategoryController::class);
		Route::resource('suppliers', SupplierController::class);
		Route::resource('invoices', InvoiceController::class);
		Route::resource('ads-campaigns', AdsCampaignController::class);
	});

require __DIR__.'/auth.php';
