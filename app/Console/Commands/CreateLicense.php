<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Services\LicenseService;
use Illuminate\Console\Command;

/**
 * LICENSE SERVER COMMAND
 * 
 * Run this command on YOUR license server to create new licenses
 * that will be stored in the database for online validation.
 */
class CreateLicense extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:create 
                            {--client= : Client name}
                            {--email= : Client email}
                            {--domain= : Domain to bind (leave empty for any domain, use * for dev)}
                            {--type=STD : License type (STD, PRO, ENT)}
                            {--expires= : Expiration date (YYYY-MM-DD) or leave empty for lifetime}
                            {--max-activations=1 : Maximum number of server activations}
                            {--features= : Comma-separated list of features}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new license in the database (for license server)';

    /**
     * Execute the console command.
     */
    public function handle(LicenseService $licenseService): int
    {
        // Interactive prompts if not provided
        $clientName = $this->option('client') ?: $this->ask('Client name');
        $clientEmail = $this->option('email') ?: $this->ask('Client email (optional)', '');
        $domain = $this->option('domain') ?: $this->ask('Domain (leave empty for any, * for dev mode)', '');
        $type = strtoupper($this->option('type'));
        $expiresDate = $this->option('expires') ?: $this->ask('Expiration date (YYYY-MM-DD, leave empty for lifetime)', '');
        $maxActivations = (int)$this->option('max-activations');
        $featuresString = $this->option('features') ?: '';

        // Validate type
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
            $expiresAt = date('Y-m-d 23:59:59', $timestamp);
        }

        // Parse features
        $features = [];
        if ($featuresString) {
            $features = array_map('trim', explode(',', $featuresString));
        }

        // Generate the license key
        $licenseKey = $licenseService->generateLicenseKey(
            $domain ?: '*',
            $type,
            $expiresDate ? strtotime($expiresDate . ' 23:59:59') : null
        );

        // Create the license in database
        $license = License::create([
            'license_key' => $licenseKey,
            'product_id' => config('license.product_id', 'redshark'),
            'client_name' => $clientName,
            'client_email' => $clientEmail ?: null,
            'domain' => $domain === '*' ? null : ($domain ?: null),
            'type' => $type,
            'status' => 'active',
            'max_activations' => $maxActivations,
            'features' => $features ?: null,
            'expires_at' => $expiresAt,
        ]);

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════════╗');
        $this->info('║                    LICENSE CREATED                               ║');
        $this->info('╠══════════════════════════════════════════════════════════════════╣');
        $this->newLine();

        $this->line("  <fg=cyan>License ID:</> #{$license->id}");
        $this->line("  <fg=cyan>Client:</> {$clientName}");
        if ($clientEmail) {
            $this->line("  <fg=cyan>Email:</> {$clientEmail}");
        }
        $this->line("  <fg=cyan>Domain:</> " . ($domain ?: 'Any domain'));
        $this->line("  <fg=cyan>Type:</> {$type}");
        $this->line("  <fg=cyan>Expires:</> " . ($expiresDate ?: 'Never (Lifetime)'));
        $this->line("  <fg=cyan>Max Activations:</> {$maxActivations}");
        if ($features) {
            $this->line("  <fg=cyan>Features:</> " . implode(', ', $features));
        }
        $this->newLine();

        $this->info('  License Key:');
        $this->newLine();
        $this->line("  <fg=green;options=bold>{$licenseKey}</>");
        $this->newLine();

        $this->info('╚══════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->comment('Send this to your client:');
        $this->newLine();
        $this->line("  LICENSE_KEY={$licenseKey}");
        $this->newLine();

        return Command::SUCCESS;
    }
}

