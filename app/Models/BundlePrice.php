<?php

namespace App\Models;

use App\Enums\PriceTier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundlePrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'is_active' => 'boolean',
        'tier' => PriceTier::class,
    ];

    /**
     * @return BelongsTo<PluginBundle, BundlePrice>
     */
    public function pluginBundle(): BelongsTo
    {
        return $this->belongsTo(PluginBundle::class);
    }

    /**
     * @param  Builder<BundlePrice>  $query
     * @return Builder<BundlePrice>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<BundlePrice>  $query
     * @return Builder<BundlePrice>
     */
    public function scopeForTier(Builder $query, PriceTier|string $tier): Builder
    {
        $tierValue = $tier instanceof PriceTier ? $tier->value : $tier;

        return $query->where('tier', $tierValue);
    }

    /**
     * @param  Builder<BundlePrice>  $query
     * @param  array<PriceTier>  $tiers
     * @return Builder<BundlePrice>
     */
    public function scopeForTiers(Builder $query, array $tiers): Builder
    {
        $tierValues = array_map(fn ($t) => $t instanceof PriceTier ? $t->value : $t, $tiers);

        return $query->whereIn('tier', $tierValues);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 2);
    }

    public function isRegularTier(): bool
    {
        return $this->tier === PriceTier::Regular;
    }

    public function isSubscriberTier(): bool
    {
        return $this->tier === PriceTier::Subscriber;
    }

    public function isEapTier(): bool
    {
        return $this->tier === PriceTier::Eap;
    }
}
