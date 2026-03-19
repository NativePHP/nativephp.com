<?php

namespace App\Models;

use App\Enums\StripeConnectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeveloperAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<User, DeveloperAccount>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Plugin>
     */
    public function plugins(): HasMany
    {
        return $this->hasMany(Plugin::class);
    }

    /**
     * @return HasMany<PluginPayout>
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(PluginPayout::class);
    }

    public function isActive(): bool
    {
        return $this->stripe_connect_status === StripeConnectStatus::Active;
    }

    public function canReceivePayouts(): bool
    {
        return $this->isActive() && $this->payouts_enabled;
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    protected function casts(): array
    {
        return [
            'stripe_connect_status' => StripeConnectStatus::class,
            'payouts_enabled' => 'boolean',
            'charges_enabled' => 'boolean',
            'onboarding_completed_at' => 'datetime',
        ];
    }
}
