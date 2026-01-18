<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class PluginBundle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

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
     * @param  Builder<PluginBundle>  $query
     * @return Builder<PluginBundle>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<PluginBundle>  $query
     * @return Builder<PluginBundle>
     */
    public function scopeFeatured(Builder $query): Builder
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
     */
    public function getRetailValueAttribute(): int
    {
        return $this->plugins
            ->filter(fn (Plugin $plugin) => $plugin->activePrice)
            ->sum(fn (Plugin $plugin) => $plugin->activePrice->amount);
    }

    /**
     * Get formatted retail value.
     */
    public function getFormattedRetailValueAttribute(): string
    {
        return '$'.number_format($this->retail_value / 100, 2);
    }

    /**
     * Get formatted bundle price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$'.number_format($this->price / 100, 2);
    }

    /**
     * Calculate the discount percentage.
     */
    public function getDiscountPercentAttribute(): int
    {
        $retailValue = $this->retail_value;

        if ($retailValue <= 0) {
            return 0;
        }

        return (int) round(($retailValue - $this->price) / $retailValue * 100);
    }

    /**
     * Get the savings amount in cents.
     */
    public function getSavingsAttribute(): int
    {
        return max(0, $this->retail_value - $this->price);
    }

    /**
     * Get formatted savings.
     */
    public function getFormattedSavingsAttribute(): string
    {
        return '$'.number_format($this->savings / 100, 2);
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
    public function calculateProportionalAllocation(): array
    {
        $retailValue = $this->retail_value;
        $bundlePrice = $this->price;
        $allocations = [];

        if ($retailValue <= 0) {
            return $allocations;
        }

        $runningTotal = 0;
        $plugins = $this->plugins->filter(fn (Plugin $p) => $p->activePrice)->values();
        $lastIndex = $plugins->count() - 1;

        foreach ($plugins as $index => $plugin) {
            $pluginRetail = $plugin->activePrice->amount;

            // For last plugin, allocate remainder to avoid rounding issues
            if ($index === $lastIndex) {
                $allocations[$plugin->id] = $bundlePrice - $runningTotal;
            } else {
                $proportion = $pluginRetail / $retailValue;
                $allocation = (int) round($bundlePrice * $proportion);
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
}
