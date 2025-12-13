<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

class CheckLicense extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:check 
                            {--refresh : Force online validation}';

    /**
     * The console command description.
     */
    protected $description = 'Check the current license status';

    /**
     * Execute the console command.
     */
    public function handle(LicenseService $licenseService): int
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════════╗');
        $this->info('║                    LICENSE STATUS                                 ║');
        $this->info('╠══════════════════════════════════════════════════════════════════╣');
        $this->newLine();

        if ($this->option('refresh')) {
            $this->comment('  Forcing online validation...');
            $licenseService->clearCache();
        }

        $info = $licenseService->getLicenseInfo();

        $statusColor = match ($info['status']) {
            'valid' => 'green',
            'not_configured' => 'yellow',
            default => 'red',
        };

        $statusIcon = match ($info['status']) {
            'valid' => '✓',
            'not_configured' => '!',
            default => '✗',
        };

        $this->line("  <fg={$statusColor}>{$statusIcon} Status:</> <fg={$statusColor};options=bold>" . strtoupper($info['status']) . "</>");
        
        if (isset($info['message'])) {
            $this->line("  <fg=cyan>Message:</> {$info['message']}");
        }

        if (isset($info['type'])) {
            $this->line("  <fg=cyan>License Type:</> {$info['type']}");
        }

        if (isset($info['expires_at'])) {
            $this->line("  <fg=cyan>Expires:</> {$info['expires_at']}");
        }

        if (isset($info['domain'])) {
            $this->line("  <fg=cyan>Domain:</> {$info['domain']}");
        }

        if (isset($info['current_domain'])) {
            $this->line("  <fg=red>Current Domain:</> {$info['current_domain']}");
        }

        if (isset($info['last_validated'])) {
            $this->line("  <fg=cyan>Last Validated:</> {$info['last_validated']}");
        }

        if (isset($info['client_name']) && $info['client_name']) {
            $this->line("  <fg=cyan>Client:</> {$info['client_name']}");
        }

        if (!empty($info['features'])) {
            $this->line("  <fg=cyan>Features:</> " . implode(', ', $info['features']));
        }

        $this->newLine();
        $this->info('╚══════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Additional server info
        $this->comment('  Server Information:');
        $this->line("  <fg=gray>Server ID:</> " . substr($licenseService->getServerId(), 0, 16) . '...');
        $this->line("  <fg=gray>Current Domain:</> " . $licenseService->getCurrentDomain());
        $this->newLine();

        return $info['status'] === 'valid' ? Command::SUCCESS : Command::FAILURE;
    }
}

