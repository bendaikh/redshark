<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * License Validation API Controller
 * 
 * THIS CONTROLLER IS FOR YOUR LICENSE SERVER (the server YOU control)
 * Deploy this on your own server where you manage client licenses.
 * 
 * Clients will call these endpoints to validate/activate their licenses.
 */
class LicenseValidationController extends Controller
{
    /**
     * Validate a license key
     */
    public function validate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
            'product_id' => 'required|string',
            'server_ip' => 'nullable|string',
            'server_id' => 'nullable|string',
        ]);

        $licenseKey = $request->input('license_key');
        $domain = $this->normalizeDomain($request->input('domain'));
        $productId = $request->input('product_id');

        // Find the license
        $license = License::where('license_key', $licenseKey)
            ->where('product_id', $productId)
            ->first();

        if (!$license) {
            return response()->json([
                'valid' => false,
                'message' => 'License not found',
            ], 404);
        }

        // Check if license is active
        if ($license->status !== 'active') {
            return response()->json([
                'valid' => false,
                'message' => 'License is ' . $license->status,
            ], 403);
        }

        // Check expiration
        if ($license->expires_at && $license->expires_at->isPast()) {
            $license->update(['status' => 'expired']);
            
            return response()->json([
                'valid' => false,
                'message' => 'License has expired',
            ], 403);
        }

        // Check domain binding
        if ($license->domain && $this->normalizeDomain($license->domain) !== $domain) {
            return response()->json([
                'valid' => false,
                'message' => 'License is not valid for this domain',
                'licensed_domain' => $license->domain,
                'requested_domain' => $domain,
            ], 403);
        }

        // Check max activations
        if ($license->max_activations > 0) {
            $activations = $license->activations()->count();
            
            if ($activations >= $license->max_activations) {
                // Check if this server is already activated
                $existingActivation = $license->activations()
                    ->where('server_id', $request->input('server_id'))
                    ->first();

                if (!$existingActivation) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Maximum activations reached',
                        'max_activations' => $license->max_activations,
                        'current_activations' => $activations,
                    ], 403);
                }
            }
        }

        // Update last validated timestamp
        $license->update([
            'last_validated_at' => now(),
            'last_validated_ip' => $request->ip(),
        ]);

        // Log the validation
        Log::info('License validated', [
            'license_id' => $license->id,
            'domain' => $domain,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'valid' => true,
            'message' => 'License is valid',
            'expires_at' => $license->expires_at?->toISOString(),
            'client_name' => $license->client_name,
            'features' => $license->features ?? [],
            'type' => $license->type,
        ]);
    }

    /**
     * Activate a license on a new domain/server
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
            'product_id' => 'required|string',
            'server_ip' => 'nullable|string',
            'server_id' => 'nullable|string',
        ]);

        $licenseKey = $request->input('license_key');
        $domain = $this->normalizeDomain($request->input('domain'));
        $productId = $request->input('product_id');
        $serverId = $request->input('server_id');

        // Find the license
        $license = License::where('license_key', $licenseKey)
            ->where('product_id', $productId)
            ->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'License not found',
            ], 404);
        }

        // Check if license is active
        if (!in_array($license->status, ['active', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'License is ' . $license->status,
            ], 403);
        }

        // Check expiration
        if ($license->expires_at && $license->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'License has expired',
            ], 403);
        }

        // If domain is not set, bind to this domain
        if (empty($license->domain)) {
            $license->update(['domain' => $domain]);
        } elseif ($this->normalizeDomain($license->domain) !== $domain) {
            return response()->json([
                'success' => false,
                'message' => 'License is already bound to a different domain',
                'licensed_domain' => $license->domain,
            ], 403);
        }

        // Check max activations and create activation record
        if ($license->max_activations > 0) {
            $existingActivation = $license->activations()
                ->where('server_id', $serverId)
                ->first();

            if (!$existingActivation) {
                $activations = $license->activations()->count();
                
                if ($activations >= $license->max_activations) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum activations reached',
                        'max_activations' => $license->max_activations,
                    ], 403);
                }

                // Create new activation
                $license->activations()->create([
                    'server_id' => $serverId,
                    'domain' => $domain,
                    'ip_address' => $request->ip(),
                    'activated_at' => now(),
                ]);
            } else {
                // Update existing activation
                $existingActivation->update([
                    'last_seen_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        // Update license status if pending
        if ($license->status === 'pending') {
            $license->update(['status' => 'active']);
        }

        $license->update([
            'last_validated_at' => now(),
            'last_validated_ip' => $request->ip(),
        ]);

        // Log the activation
        Log::info('License activated', [
            'license_id' => $license->id,
            'domain' => $domain,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'License activated successfully',
            'expires_at' => $license->expires_at?->toISOString(),
            'client_name' => $license->client_name,
            'features' => $license->features ?? [],
            'type' => $license->type,
        ]);
    }

    /**
     * Deactivate/revoke a license (admin endpoint)
     */
    public function revoke(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'admin_secret' => 'required|string',
        ]);

        // Verify admin secret
        if ($request->input('admin_secret') !== config('license.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $license = License::where('license_key', $request->input('license_key'))->first();

        if (!$license) {
            return response()->json(['error' => 'License not found'], 404);
        }

        $license->update(['status' => 'revoked']);

        return response()->json([
            'success' => true,
            'message' => 'License revoked successfully',
        ]);
    }

    /**
     * Normalize domain for comparison
     */
    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $domain);
        $domain = rtrim($domain, '/');
        $domain = explode(':', $domain)[0]; // Remove port
        
        return $domain;
    }
}

