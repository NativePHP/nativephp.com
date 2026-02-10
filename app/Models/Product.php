<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * @return HasMany<ProductPrice>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * @return HasOne<ProductPrice>
     */
    public function activePrice(): HasOne
    {
        return $this->hasOne(ProductPrice::class)->where('is_active', true)->latest();
    }

    /**
     * @return HasMany<ProductLicense>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(ProductLicense::class);
    }

    /**
     * Get the best (lowest) active price for a user based on their eligible tiers.
     * Returns null if no price exists for the user's eligible tiers.
     */
    public function getBestPriceForUser(?User $user): ?ProductPrice
    {
        $eligibleTiers = $user ? $user->getEligiblePriceTiers() : [\App\Enums\PriceTier::Regular];

        return $this->prices()
            ->active()
            ->forTiers($eligibleTiers)
            ->orderBy('amount', 'asc')
            ->first();
    }

    /**
     * Check if a user has access to at least one price tier for this product.
     */
    public function hasAccessiblePriceFor(?User $user): bool
    {
        return $this->getBestPriceForUser($user) !== null;
    }

    /**
     * Get the regular (non-discounted) price for comparison display.
     */
    public function getRegularPrice(): ?ProductPrice
    {
        return $this->prices()
            ->active()
            ->forTier(\App\Enums\PriceTier::Regular)
            ->first() ?? $this->activePrice;
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
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

    /**
     * Check if a user owns this product.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->licenses()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Check if this product grants GitHub repository access.
     */
    public function hasGitHubRepoAccess(): bool
    {
        return ! empty($this->github_repo);
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
     * Get formatted price (uses regular tier price).
     */
    public function getFormattedPriceAttribute(): string
    {
        $price = $this->getRegularPrice();

        $amount = $price ? $price->amount : 0;

        return '$'.number_format($amount / 100, 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
