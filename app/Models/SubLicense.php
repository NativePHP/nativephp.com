<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubLicense extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_suspended' => 'boolean',
    ];

    protected $fillable = [
        'parent_license_id',
        'anystack_id',
        'name',
        'key',
        'assigned_email',
        'is_suspended',
        'expires_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $subLicense) {
            // Sub-licenses must always come from Anystack with real license keys
            // Never auto-generate keys locally

            // Set expiry to match parent license only if not explicitly set
            if ($subLicense->parentLicense && $subLicense->parentLicense->expires_at && ! $subLicense->expires_at) {
                $subLicense->expires_at = $subLicense->parentLicense->expires_at;
            }
        });
    }

    /**
     * @return BelongsTo<License>
     */
    public function parentLicense(): BelongsTo
    {
        return $this->belongsTo(License::class, 'parent_license_id');
    }

    public function scopeWhereActive(Builder $builder): Builder
    {
        return $builder->where(fn ($where) => $where
            ->where('is_suspended', false)
            ->where(fn ($expiry) => $expiry
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now())
            )
        );
    }

    public function scopeWhereSuspended(Builder $builder): Builder
    {
        return $builder->where('is_suspended', true);
    }

    public function scopeWhereExpired(Builder $builder): Builder
    {
        return $builder->where('is_suspended', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function isActive(): bool
    {
        return ! $this->is_suspended &&
               (! $this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return ! $this->is_suspended &&
               $this->expires_at &&
               $this->expires_at->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_suspended) {
            return 'Suspended';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Expired';
        }

        return 'Active';
    }

    public function suspend(): bool
    {
        return $this->update(['is_suspended' => true]);
    }

}
