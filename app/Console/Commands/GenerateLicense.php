<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

class GenerateLicense extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:generate 
                            {domain : The domain to generate the license for (use * for development)}
                            {--type=STD : License type (STD, PRO, ENT)}
                            {--expires= : Expiration date (YYYY-MM-DD) or leave empty for lifetime}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a new license key for a client';

    /**
     * Execute the console command.
     */
    public function handle(LicenseService $licenseService): int
    {
        $domain = $this->argument('domain');
        $type = strtoupper($this->option('type'));
        $expiresDate = $this->option('expires');

        // Validate license type
        if (!in_array($type, ['STD', 'PRO', 'ENT'])) {
            $this->error('Invalid license type. Use STD, PRO, or ENT.');
            return Command::FAILURE;
        }

        // Parse expiration date
        $expiresAt = null;
        if ($expiresDate) {
            $timestamp = strtotime($expiresDate);
            if ($timestamp === false) {
                $this->error('Invalid expiration date format. Use YYYY-MM-DD.');
                return Command::FAILURE;
            }
            // Set to end of day
            $expiresAt = strtotime($expiresDate . ' 23:59:59');
        }

        // Generate the license key
        $licenseKey = $licenseService->generateLicenseKey($domain, $type, $expiresAt);

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════════╗');
        $this->info('║                    LICENSE KEY GENERATED                          ║');
        $this->info('╠══════════════════════════════════════════════════════════════════╣');
        $this->newLine();
        
        $this->line("  <fg=cyan>Domain:</> {$domain}");
        $this->line("  <fg=cyan>Type:</> {$type}");
        $this->line("  <fg=cyan>Expires:</> " . ($expiresDate ?: 'Never (Lifetime)'));
        $this->newLine();
        
        $this->info('  License Key:');
        $this->newLine();
        $this->line("  <fg=green;options=bold>{$licenseKey}</>");
        $this->newLine();
        
        $this->info('╚══════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->comment('Instructions for client:');
        $this->line('1. Add this to the .env file:');
        $this->line("   LICENSE_KEY={$licenseKey}");
        if ($domain !== '*') {
            $this->line("   LICENSE_DOMAIN={$domain}");
        }
        $this->newLine();
        $this->line('2. Or enter the key in the license activation page after installation.');
        $this->newLine();

        // Copy to clipboard if possible (Windows)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("echo {$licenseKey} | clip");
            $this->info('✓ License key copied to clipboard!');
        }

        return Command::SUCCESS;
    }
}

