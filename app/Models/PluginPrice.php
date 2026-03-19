<?php

namespace App\Models;

use App\Enums\PriceTier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginPrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<Plugin, PluginPrice>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @param  Builder<PluginPrice>  $query
     * @return Builder<PluginPrice>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<PluginPrice>  $query
     * @return Builder<PluginPrice>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forTier(Builder $query, PriceTier|string $tier): Builder
    {
        $tierValue = $tier instanceof PriceTier ? $tier->value : $tier;

        return $query->where('tier', $tierValue);
    }

    /**
     * @param  Builder<PluginPrice>  $query
     * @param  array<PriceTier>  $tiers
     * @return Builder<PluginPrice>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forTiers(Builder $query, array $tiers): Builder
    {
        $tierValues = array_map(fn ($t) => $t instanceof PriceTier ? $t->value : $t, $tiers);

        return $query->whereIn('tier', $tierValues);
    }

    protected function formattedAmount(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return number_format($this->amount / 100, 2);
        });
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

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'is_active' => 'boolean',
            'tier' => PriceTier::class,
        ];
    }
}
