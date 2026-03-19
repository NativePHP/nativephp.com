<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class PluginBundle extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsToMany<Plugin>
     */
    public function plugins(): BelongsToMany
    {
        return $this->belongsToMany(Plugin::class, 'bundle_plugin')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * @return HasMany<PluginLicense>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(PluginLicense::class);
    }

    /**
     * @return HasMany<BundlePrice>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(BundlePrice::class);
    }

    /**
     * Get the active price, preferring the Regular tier for consistency.
     *
     * @return HasOne<BundlePrice>
     */
    public function activePrice(): HasOne
    {
        return $this->hasOne(BundlePrice::class)
            ->where('is_active', true)
            ->orderByRaw("CASE WHEN tier = 'regular' THEN 0 ELSE 1 END")
            ->latest();
    }

    /**
     * Get the best (lowest) active price for a user based on their eligible tiers.
     * Returns null if no price exists for the user's eligible tiers.
     */
    public function getBestPriceForUser(?User $user): ?BundlePrice
    {
        $eligibleTiers = $user ? $user->getEligiblePriceTiers() : [\App\Enums\PriceTier::Regular];

        // Get the lowest active price for the user's eligible tiers
        return $this->prices()
            ->active()
            ->forTiers($eligibleTiers)
            ->orderBy('amount', 'asc')
            ->first();
    }

    /**
     * Check if a user has access to at least one price tier for this bundle.
     */
    public function hasAccessiblePriceFor(?User $user): bool
    {
        return $this->getBestPriceForUser($user) !== null;
    }

    /**
     * Get the regular (non-discounted) price for comparison display.
     */
    public function getRegularPrice(): ?BundlePrice
    {
        return $this->prices()
            ->active()
            ->forTier(\App\Enums\PriceTier::Regular)
            ->first() ?? $this->activePrice;
    }

    /**
     * @param  Builder<PluginBundle>  $query
     * @return Builder<PluginBundle>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<PluginBundle>  $query
     * @return Builder<PluginBundle>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function isActive(): bool
    {
        return $this->is_active
            && $this->published_at
            && $this->published_at->lte(now());
    }

    public function getLogoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return asset('storage/'.$this->logo_path);
    }

    public function hasLogo(): bool
    {
        return $this->logo_path !== null;
    }

    /**
     * Calculate the total retail value of all plugins in the bundle.
     * Uses regular tier prices for comparison purposes.
     */
    protected function retailValue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return $this->plugins
                ->filter(fn (Plugin $plugin) => $plugin->getRegularPrice())
                ->sum(fn (Plugin $plugin) => $plugin->getRegularPrice()->amount);
        });
    }

    /**
     * Get formatted retail value.
     */
    protected function formattedRetailValue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return '$'.number_format($this->retail_value / 100, 2);
        });
    }

    /**
     * Get formatted bundle price for a user's best available tier.
     */
    public function getFormattedPriceForUser(?User $user): string
    {
        $price = $this->getBestPriceForUser($user);

        $amount = $price ? $price->amount : $this->price;

        return '$'.number_format($amount / 100, 2);
    }

    /**
     * Get formatted bundle price (uses regular tier price or legacy price column).
     */
    protected function formattedPrice(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $price = $this->getRegularPrice();
            $amount = $price ? $price->amount : $this->price;

            return '$'.number_format($amount / 100, 2);
        });
    }

    /**
     * Calculate the discount percentage for a given price amount.
     */
    public function getDiscountPercentFor(int $priceAmount): int
    {
        $retailValue = $this->retail_value;

        if ($retailValue <= 0) {
            return 0;
        }

        return (int) round(($retailValue - $priceAmount) / $retailValue * 100);
    }

    /**
     * Calculate the discount percentage for a user's best price.
     */
    public function getDiscountPercentForUser(?User $user): int
    {
        $price = $this->getBestPriceForUser($user);
        $amount = $price ? $price->amount : $this->price;

        return $this->getDiscountPercentFor($amount);
    }

    /**
     * Calculate the discount percentage (uses regular price for backwards compatibility).
     */
    protected function discountPercent(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $price = $this->getRegularPrice();
            $amount = $price ? $price->amount : $this->price;

            return $this->getDiscountPercentFor($amount);
        });
    }

    /**
     * Get the savings amount in cents for a given price amount.
     */
    public function getSavingsFor(int $priceAmount): int
    {
        return max(0, $this->retail_value - $priceAmount);
    }

    /**
     * Get the savings amount in cents for a user's best price.
     */
    public function getSavingsForUser(?User $user): int
    {
        $price = $this->getBestPriceForUser($user);
        $amount = $price ? $price->amount : $this->price;

        return $this->getSavingsFor($amount);
    }

    /**
     * Get the savings amount in cents (uses regular price for backwards compatibility).
     */
    protected function savings(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $price = $this->getRegularPrice();
            $amount = $price ? $price->amount : $this->price;

            return $this->getSavingsFor($amount);
        });
    }

    /**
     * Get formatted savings for a given amount.
     */
    public function getFormattedSavingsFor(int $priceAmount): string
    {
        return '$'.number_format($this->getSavingsFor($priceAmount) / 100, 2);
    }

    /**
     * Get formatted savings for a user's best price.
     */
    public function getFormattedSavingsForUser(?User $user): string
    {
        return '$'.number_format($this->getSavingsForUser($user) / 100, 2);
    }

    /**
     * Get formatted savings (uses regular price for backwards compatibility).
     */
    protected function formattedSavings(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return '$'.number_format($this->savings / 100, 2);
        });
    }

    /**
     * Check if a user already owns all plugins in the bundle.
     */
    public function isOwnedBy(User $user): bool
    {
        $ownedPluginIds = $user->pluginLicenses()
            ->active()
            ->pluck('plugin_id')
            ->toArray();

        return $this->plugins->every(
            fn (Plugin $plugin) => in_array($plugin->id, $ownedPluginIds)
        );
    }

    /**
     * Get plugins the user doesn't own yet.
     *
     * @return Collection<int, Plugin>
     */
    public function getUnownedPluginsFor(User $user): Collection
    {
        $ownedPluginIds = $user->pluginLicenses()
            ->active()
            ->pluck('plugin_id')
            ->toArray();

        return $this->plugins->filter(
            fn (Plugin $plugin) => ! in_array($plugin->id, $ownedPluginIds)
        );
    }

    /**
     * Calculate proportional allocation of bundle price to each plugin.
     * Used for developer payouts.
     *
     * @return array<int, int> Plugin ID => allocated amount in cents
     */
    public function calculateProportionalAllocation(?int $bundlePriceAmount = null): array
    {
        $retailValue = $this->retail_value;

        // Use provided amount or fall back to regular tier price or legacy price
        if ($bundlePriceAmount === null) {
            $price = $this->getRegularPrice();
            $bundlePriceAmount = $price ? $price->amount : $this->price;
        }

        $allocations = [];

        if ($retailValue <= 0) {
            return $allocations;
        }

        $runningTotal = 0;
        $plugins = $this->plugins->filter(fn (Plugin $p) => $p->getRegularPrice())->values();
        $lastIndex = $plugins->count() - 1;

        foreach ($plugins as $index => $plugin) {
            $pluginRetail = $plugin->getRegularPrice()->amount;

            // For last plugin, allocate remainder to avoid rounding issues
            if ($index === $lastIndex) {
                $allocations[$plugin->id] = $bundlePriceAmount - $runningTotal;
            } else {
                $proportion = $pluginRetail / $retailValue;
                $allocation = (int) round($bundlePriceAmount * $proportion);
                $allocations[$plugin->id] = $allocation;
                $runningTotal += $allocation;
            }
        }

        return $allocations;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
