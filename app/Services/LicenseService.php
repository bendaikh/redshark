<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class LicenseService
{
    private string $licenseKey;
    private string $domain;
    private string $secret;
    private string $serverUrl;
    private string $productId;
    private int $gracePeriod;
    private int $validationInterval;
    private string $cacheFile;

    public function __construct()
    {
        $this->licenseKey = config('license.key', '');
        $this->domain = config('license.domain', '') ?: $this->getCurrentDomain();
        $this->secret = config('license.secret');
        $this->serverUrl = config('license.server_url');
        $this->productId = config('license.product_id');
        $this->gracePeriod = config('license.grace_period', 7);
        $this->validationInterval = config('license.validation_interval', 24);
        $this->cacheFile = config('license.cache_file');
    }

    /**
     * Check if the application has a valid license
     */
    public function isValid(): bool
    {
        // No license key configured
        if (empty($this->licenseKey)) {
            return false;
        }

        // First, validate the license key format and signature locally
        if (!$this->validateKeyFormat()) {
            return false;
        }

        // Check domain binding
        if (!$this->validateDomain()) {
            return false;
        }

        // Check if we need to validate online
        if ($this->needsOnlineValidation()) {
            return $this->validateOnline();
        }

        // Use cached validation result
        return $this->getCachedValidation();
    }

    /**
     * Validate the license key format and signature locally
     */
    public function validateKeyFormat(): bool
    {
        try {
            $parts = explode('-', $this->licenseKey);
            
            if (count($parts) < 5) {
                return false;
            }

            // Extract components
            $prefix = $parts[0]; // Product prefix
            $type = $parts[1];   // License type (STD/PRO/ENT)
            $timestamp = $parts[2]; // Expiry timestamp
            $domainHash = $parts[3]; // Domain hash
            $signature = implode('-', array_slice($parts, 4)); // Signature

            // Verify prefix
            if ($prefix !== strtoupper(substr($this->productId, 0, 4))) {
                return false;
            }

            // Verify expiry
            if ($timestamp !== 'LIFETIME' && (int)$timestamp < time()) {
                return false;
            }

            // Verify signature
            $payload = "{$prefix}-{$type}-{$timestamp}-{$domainHash}";
            $expectedSignature = $this->generateSignature($payload);

            return hash_equals($expectedSignature, $signature);
        } catch (Exception $e) {
            Log::error('License validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate that the current domain matches the license
     */
    public function validateDomain(): bool
    {
        try {
            $parts = explode('-', $this->licenseKey);
            
            if (count($parts) < 4) {
                return false;
            }

            $domainHash = $parts[3];
            $currentDomainHash = $this->hashDomain($this->getCurrentDomain());

            // Allow wildcard domain hash for development
            if ($domainHash === 'DEVMODE') {
                return true;
            }

            return hash_equals($domainHash, $currentDomainHash);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if online validation is needed
     */
    private function needsOnlineValidation(): bool
    {
        $cached = $this->getLicenseCache();

        if (empty($cached) || !isset($cached['last_validated'])) {
            return true;
        }

        $hoursSinceValidation = (time() - $cached['last_validated']) / 3600;

        return $hoursSinceValidation >= $this->validationInterval;
    }

    /**
     * Validate license with the licensing server
     */
    public function validateOnline(): bool
    {
        try {
            $response = Http::timeout(10)->post("{$this->serverUrl}/api/license/validate", [
                'license_key' => $this->licenseKey,
                'domain' => $this->getCurrentDomain(),
                'product_id' => $this->productId,
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
                'server_id' => $this->getServerId(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['valid'] ?? false) {
                    $this->saveLicenseCache([
                        'valid' => true,
                        'last_validated' => time(),
                        'expires_at' => $data['expires_at'] ?? null,
                        'features' => $data['features'] ?? [],
                        'client_name' => $data['client_name'] ?? '',
                    ]);
                    return true;
                } else {
                    $this->saveLicenseCache([
                        'valid' => false,
                        'last_validated' => time(),
                        'error' => $data['message'] ?? 'Invalid license',
                    ]);
                    return false;
                }
            }

            // Server unreachable, use grace period
            return $this->handleOfflineValidation();
        } catch (Exception $e) {
            Log::warning('License server unreachable: ' . $e->getMessage());
            return $this->handleOfflineValidation();
        }
    }

    /**
     * Handle validation when license server is unreachable
     */
    private function handleOfflineValidation(): bool
    {
        $cached = $this->getLicenseCache();

        // Never validated successfully before
        if (empty($cached) || !($cached['valid'] ?? false)) {
            return false;
        }

        // Check grace period
        $daysSinceValidation = (time() - $cached['last_validated']) / 86400;

        if ($daysSinceValidation > $this->gracePeriod) {
            return false;
        }

        // Still within grace period
        return true;
    }

    /**
     * Get cached validation result
     */
    private function getCachedValidation(): bool
    {
        $cached = $this->getLicenseCache();
        return $cached['valid'] ?? false;
    }

    /**
     * Get the license cache
     */
    private function getLicenseCache(): array
    {
        if (!file_exists($this->cacheFile)) {
            return [];
        }

        try {
            $content = file_get_contents($this->cacheFile);
            $data = json_decode($content, true);
            
            // Verify cache integrity
            if (!isset($data['checksum'])) {
                return [];
            }

            $checksum = $data['checksum'];
            unset($data['checksum']);
            
            if (!hash_equals($this->generateCacheChecksum($data), $checksum)) {
                return [];
            }

            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Save license cache
     */
    private function saveLicenseCache(array $data): void
    {
        $data['checksum'] = $this->generateCacheChecksum($data);
        
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->cacheFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Generate cache checksum to prevent tampering
     */
    private function generateCacheChecksum(array $data): string
    {
        return hash_hmac('sha256', json_encode($data), $this->secret . $this->getServerId());
    }

    /**
     * Generate a license key
     */
    public function generateLicenseKey(
        string $domain,
        string $type = 'STD',
        ?int $expiresAt = null
    ): string {
        $prefix = strtoupper(substr($this->productId, 0, 4));
        $timestamp = $expiresAt ? (string)$expiresAt : 'LIFETIME';
        $domainHash = $domain === '*' ? 'DEVMODE' : $this->hashDomain($domain);

        $payload = "{$prefix}-{$type}-{$timestamp}-{$domainHash}";
        $signature = $this->generateSignature($payload);

        return "{$payload}-{$signature}";
    }

    /**
     * Generate signature for a payload
     */
    private function generateSignature(string $payload): string
    {
        $hash = hash_hmac('sha256', $payload, $this->secret);
        // Format as readable chunks
        return strtoupper(substr($hash, 0, 8) . '-' . substr($hash, 8, 8) . '-' . substr($hash, 16, 8));
    }

    /**
     * Hash a domain for license binding
     */
    private function hashDomain(string $domain): string
    {
        // Normalize domain
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $domain);
        $domain = rtrim($domain, '/');

        return strtoupper(substr(hash_hmac('sha256', $domain, $this->secret), 0, 8));
    }

    /**
     * Get the current domain
     */
    public function getCurrentDomain(): string
    {
        return $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    }

    /**
     * Get a unique server identifier
     */
    public function getServerId(): string
    {
        // Create a unique identifier based on server characteristics
        $factors = [
            php_uname('n'), // Hostname
            $_SERVER['SERVER_ADDR'] ?? '',
            $_SERVER['DOCUMENT_ROOT'] ?? '',
            base_path(),
        ];

        return hash('sha256', implode('|', $factors));
    }

    /**
     * Get license information
     */
    public function getLicenseInfo(): array
    {
        if (empty($this->licenseKey)) {
            return [
                'status' => 'not_configured',
                'message' => 'No license key configured',
            ];
        }

        $cached = $this->getLicenseCache();

        if (!$this->validateKeyFormat()) {
            return [
                'status' => 'invalid',
                'message' => 'Invalid license key format or signature',
            ];
        }

        if (!$this->validateDomain()) {
            return [
                'status' => 'domain_mismatch',
                'message' => 'License is not valid for this domain',
                'current_domain' => $this->getCurrentDomain(),
            ];
        }

        $parts = explode('-', $this->licenseKey);
        $expiresAt = $parts[2] ?? '';

        return [
            'status' => $this->isValid() ? 'valid' : 'invalid',
            'type' => $parts[1] ?? 'UNKNOWN',
            'expires_at' => $expiresAt === 'LIFETIME' ? 'Never' : date('Y-m-d H:i:s', (int)$expiresAt),
            'domain' => $this->getCurrentDomain(),
            'last_validated' => isset($cached['last_validated']) 
                ? date('Y-m-d H:i:s', $cached['last_validated']) 
                : 'Never',
            'client_name' => $cached['client_name'] ?? '',
            'features' => $cached['features'] ?? [],
        ];
    }

    /**
     * Activate a license
     */
    public function activate(string $licenseKey): array
    {
        $this->licenseKey = $licenseKey;
        
        // Validate format first
        if (!$this->validateKeyFormat()) {
            return [
                'success' => false,
                'message' => 'Invalid license key format',
            ];
        }

        // Validate domain
        if (!$this->validateDomain()) {
            return [
                'success' => false,
                'message' => 'This license is not valid for this domain: ' . $this->getCurrentDomain(),
            ];
        }

        // Try online validation
        try {
            $response = Http::timeout(10)->post("{$this->serverUrl}/api/license/activate", [
                'license_key' => $licenseKey,
                'domain' => $this->getCurrentDomain(),
                'product_id' => $this->productId,
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
                'server_id' => $this->getServerId(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    $this->saveLicenseCache([
                        'valid' => true,
                        'last_validated' => time(),
                        'expires_at' => $data['expires_at'] ?? null,
                        'features' => $data['features'] ?? [],
                        'client_name' => $data['client_name'] ?? '',
                    ]);

                    return [
                        'success' => true,
                        'message' => 'License activated successfully!',
                        'data' => $data,
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Activation failed',
                ];
            }
        } catch (Exception $e) {
            // Server unreachable, allow offline activation if key is valid
            Log::info('License server unreachable during activation, using offline mode');
        }

        // Offline activation (if key format is valid)
        $this->saveLicenseCache([
            'valid' => true,
            'last_validated' => time(),
            'offline_activation' => true,
        ]);

        return [
            'success' => true,
            'message' => 'License activated in offline mode. Online validation will occur when server is reachable.',
        ];
    }

    /**
     * Clear license cache
     */
    public function clearCache(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }
}

