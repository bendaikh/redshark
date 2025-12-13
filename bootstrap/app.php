<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetCurrentCountry;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\LicenseMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register license routes (excluded from license middleware)
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/license.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // License validation middleware - runs on all web routes
        // Excludes routes defined in config/license.php 'excluded_routes'
        $middleware->appendToGroup('web', [
            LicenseMiddleware::class,
            SetLocale::class,
            SetCurrentCountry::class,
        ]);

		// Spatie Permission middleware aliases (Laravel 11 style)
		$middleware->alias([
			'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
			'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
			'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'license' => LicenseMiddleware::class,
		]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
