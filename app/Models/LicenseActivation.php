<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * License Activation Model
 * 
 * Tracks where a license has been activated (for multi-activation support)
 */
class LicenseActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'server_id',
        'domain',
        'ip_address',
        'hostname',
        'activated_at',
        'last_seen_at',
        'metadata',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the license this activation belongs to
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Check if this activation is still active (seen in last 7 days)
     */
    public function isActive(): bool
    {
        $lastSeen = $this->last_seen_at ?? $this->activated_at;
        
        return $lastSeen && $lastSeen->isAfter(now()->subDays(7));
    }
}

