<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * License Model
 * 
 * THIS MODEL IS FOR YOUR LICENSE SERVER (the server YOU control)
 * Use this to manage client licenses in your admin panel.
 */
class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key',
        'product_id',
        'client_name',
        'client_email',
        'domain',
        'type',
        'status',
        'max_activations',
        'features',
        'expires_at',
        'last_validated_at',
        'last_validated_ip',
        'notes',
    ];

    protected $casts = [
        'features' => 'array',
        'expires_at' => 'datetime',
        'last_validated_at' => 'datetime',
        'max_activations' => 'integer',
    ];

    /**
     * License status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REVOKED = 'revoked';

    /**
     * License type constants
     */
    const TYPE_STANDARD = 'STD';
    const TYPE_PROFESSIONAL = 'PRO';
    const TYPE_ENTERPRISE = 'ENT';

    /**
     * Get all activations for this license
     */
    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    /**
     * Check if license is valid
     */
    public function isValid(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if license is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get remaining days until expiration
     */
    public function daysRemaining(): ?int
    {
        if (!$this->expires_at) {
            return null; // Lifetime license
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    /**
     * Get activation count
     */
    public function getActivationCountAttribute(): int
    {
        return $this->activations()->count();
    }

    /**
     * Scope for active licenses
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for expiring soon (within 30 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->where('expires_at', '>', now());
    }
}

