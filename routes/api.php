<?php

use App\Http\Controllers\Api\LicenseValidationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - LICENSE SERVER
|--------------------------------------------------------------------------
|
| These routes are for YOUR LICENSE SERVER.
| Clients will call these endpoints to validate their licenses.
|
| If you're deploying this as a CLIENT INSTALLATION, you don't need
| these routes. They're only for the server YOU control.
|
*/

Route::prefix('license')->group(function () {
    // License validation endpoint (called by clients periodically)
    Route::post('/validate', [LicenseValidationController::class, 'validate'])
        ->name('api.license.validate');

    // License activation endpoint (called when client first installs)
    Route::post('/activate', [LicenseValidationController::class, 'activate'])
        ->name('api.license.activate');

    // License revocation endpoint (admin only - requires secret)
    Route::post('/revoke', [LicenseValidationController::class, 'revoke'])
        ->name('api.license.revoke');
});

