<?php

use App\Http\Controllers\LicenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| License Routes
|--------------------------------------------------------------------------
|
| These routes handle license activation and validation.
| They are excluded from the license middleware.
|
*/

Route::get('/license/activate', [LicenseController::class, 'showActivation'])
    ->name('license.activate');

Route::post('/license/activate', [LicenseController::class, 'activate'])
    ->name('license.store');

Route::get('/api/license/status', [LicenseController::class, 'status'])
    ->name('license.status');

Route::get('/api/license/check', [LicenseController::class, 'check'])
    ->name('license.check');

