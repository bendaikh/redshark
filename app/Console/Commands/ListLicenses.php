<?php

namespace App\Console\Commands;

use App\Models\License;
use Illuminate\Console\Command;

/**
 * LICENSE SERVER COMMAND
 * 
 * Run this command on YOUR license server to list all licenses.
 */
class ListLicenses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:list 
                            {--status= : Filter by status (active, expired, revoked, suspended)}
                            {--type= : Filter by type (STD, PRO, ENT)}
                            {--expiring : Show only licenses expiring within 30 days}';

    /**
     * The console command description.
     */
    protected $description = 'List all licenses in the database (for license server)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = License::query();

        if ($status = $this->option('status')) {
            $query->where('status', $status);
        }

        if ($type = $this->option('type')) {
            $query->where('type', strtoupper($type));
        }

        if ($this->option('expiring')) {
            $query->expiringSoon();
        }

        $licenses = $query->orderBy('created_at', 'desc')->get();

        if ($licenses->isEmpty()) {
            $this->warn('No licenses found.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info("Found {$licenses->count()} license(s):");
        $this->newLine();

        $headers = ['ID', 'Client', 'Domain', 'Type', 'Status', 'Expires', 'Activations', 'Last Validated'];

        $rows = $licenses->map(function ($license) {
            $statusColor = match ($license->status) {
                'active' => 'green',
                'expired', 'revoked' => 'red',
                'suspended' => 'yellow',
                default => 'gray',
            };

            return [
                $license->id,
                substr($license->client_name, 0, 20),
                $license->domain ?: 'Any',
                $license->type,
                "<fg={$statusColor}>" . strtoupper($license->status) . "</>",
                $license->expires_at ? $license->expires_at->format('Y-m-d') : 'Never',
                $license->activations()->count() . '/' . $license->max_activations,
                $license->last_validated_at ? $license->last_validated_at->diffForHumans() : 'Never',
            ];
        });

        $this->table($headers, $rows);

        return Command::SUCCESS;
    }
}

