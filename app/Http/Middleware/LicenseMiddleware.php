<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseMiddleware
{
    public function __construct(
        private LicenseService $licenseService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this route is excluded from license check
        $excludedRoutes = config('license.excluded_routes', []);
        $currentRoute = $request->route()?->getName();

        if ($currentRoute && in_array($currentRoute, $excludedRoutes)) {
            return $next($request);
        }

        // Check if license is valid
        if (!$this->licenseService->isValid()) {
            // Check if license key is configured at all
            $licenseKey = config('license.key');

            if (empty($licenseKey)) {
                // No license configured, redirect to activation page
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'License required',
                        'message' => 'Please activate your license to use this application.',
                    ], 403);
                }

                return redirect()->route('license.activate')
                    ->with('error', 'Please activate your license to use this application.');
            }

            // License is invalid
            $info = $this->licenseService->getLicenseInfo();

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Invalid license',
                    'message' => $info['message'] ?? 'Your license is invalid or has expired.',
                    'status' => $info['status'] ?? 'invalid',
                ], 403);
            }

            return redirect()->route('license.activate')
                ->with('error', $info['message'] ?? 'Your license is invalid or has expired.')
                ->with('license_status', $info['status'] ?? 'invalid');
        }

        return $next($request);
    }
}

