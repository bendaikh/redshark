<?php

namespace App\Http\Controllers;

use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LicenseController extends Controller
{
    public function __construct(
        private LicenseService $licenseService
    ) {}

    /**
     * Show the license activation page
     */
    public function showActivation()
    {
        $info = $this->licenseService->getLicenseInfo();
        $currentDomain = $this->licenseService->getCurrentDomain();

        return view('license.activate', [
            'licenseInfo' => $info,
            'currentDomain' => $currentDomain,
        ]);
    }

    /**
     * Process license activation
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|min:20',
        ]);

        $licenseKey = trim($request->input('license_key'));
        
        $result = $this->licenseService->activate($licenseKey);

        if ($result['success']) {
            // Update the .env file with the license key
            $this->updateEnvFile('LICENSE_KEY', $licenseKey);
            $this->updateEnvFile('LICENSE_DOMAIN', $this->licenseService->getCurrentDomain());

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('dashboard')
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Show license status (API endpoint)
     */
    public function status()
    {
        return response()->json($this->licenseService->getLicenseInfo());
    }

    /**
     * Check license validity (API endpoint)
     */
    public function check()
    {
        return response()->json([
            'valid' => $this->licenseService->isValid(),
            'info' => $this->licenseService->getLicenseInfo(),
        ]);
    }

    /**
     * Update the .env file with a key-value pair
     */
    private function updateEnvFile(string $key, string $value): void
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $content = file_get_contents($envFile);

        // Check if key exists
        if (preg_match("/^{$key}=.*/m", $content)) {
            // Update existing key
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $content
            );
        } else {
            // Add new key
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($envFile, $content);
    }
}

