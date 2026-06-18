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

    public const CURRENT_PLUGIN_TERMS_VERSION = '1.0';

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

    public function hasAcceptedPluginTerms(): bool
    {
        return $this->accepted_plugin_terms_at !== null;
    }

    public function hasAcceptedCurrentTerms(): bool
    {
        return $this->hasAcceptedPluginTerms()
            && $this->plugin_terms_version === self::CURRENT_PLUGIN_TERMS_VERSION;
    }

    public function platformFeePercent(): int
    {
        return 100 - $this->payout_percentage;
    }

    protected function casts(): array
    {
        return [
            'stripe_connect_status' => StripeConnectStatus::class,
            'payouts_enabled' => 'boolean',
            'charges_enabled' => 'boolean',
            'onboarding_completed_at' => 'datetime',
            'accepted_plugin_terms_at' => 'datetime',
            'payout_percentage' => 'integer',
        ];
    }
}
