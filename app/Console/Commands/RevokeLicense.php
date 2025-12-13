<?php

namespace App\Console\Commands;

use App\Models\License;
use Illuminate\Console\Command;

/**
 * LICENSE SERVER COMMAND
 * 
 * Run this command on YOUR license server to revoke a license.
 */
class RevokeLicense extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:revoke 
                            {license : License ID or license key}
                            {--force : Skip confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Revoke a license (for license server)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('license');

        // Find by ID or license key
        $license = License::where('id', $identifier)
            ->orWhere('license_key', $identifier)
            ->first();

        if (!$license) {
            $this->error('License not found.');
            return Command::FAILURE;
        }

        $this->newLine();
        $this->warn('You are about to revoke the following license:');
        $this->newLine();
        $this->line("  <fg=cyan>ID:</> #{$license->id}");
        $this->line("  <fg=cyan>Client:</> {$license->client_name}");
        $this->line("  <fg=cyan>Domain:</> " . ($license->domain ?: 'Any'));
        $this->line("  <fg=cyan>Status:</> {$license->status}");
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Are you sure you want to revoke this license?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $license->update(['status' => 'revoked']);

        $this->newLine();
        $this->info('✓ License has been revoked.');
        $this->comment('The client will no longer be able to use this license.');
        $this->newLine();

        return Command::SUCCESS;
    }
}

