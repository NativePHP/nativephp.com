<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, Cart>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CartItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public function itemCount(): int
    {
        return $this->items()->count();
    }

    public function hasPlugin(Plugin $plugin): bool
    {
        return $this->items()->where('plugin_id', $plugin->id)->exists();
    }

    public function hasBundle(PluginBundle $bundle): bool
    {
        return $this->items()->where('plugin_bundle_id', $bundle->id)->exists();
    }

    public function getSubtotal(): int
    {
        return $this->items->sum(fn (CartItem $item) => $item->getItemPrice());
    }

    public function getFormattedSubtotal(): string
    {
        return '$'.number_format($this->getSubtotal() / 100);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function assignToUser(User $user): void
    {
        $this->update([
            'user_id' => $user->id,
            'session_id' => null,
        ]);
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    /**
     * Find all bundles that contain at least one plugin from the cart.
     *
     * @return \Illuminate\Support\Collection<int, PluginBundle>
     */
    public function getAvailableBundleUpgrades(): \Illuminate\Support\Collection
    {
        // Get plugin IDs that are in the cart as individual items (not part of a bundle)
        $cartPluginIds = $this->items()
            ->whereNotNull('plugin_id')
            ->pluck('plugin_id')
            ->toArray();

        if (empty($cartPluginIds)) {
            return collect();
        }

        // Get bundle IDs already in the cart
        $cartBundleIds = $this->items()
            ->whereNotNull('plugin_bundle_id')
            ->pluck('plugin_bundle_id')
            ->toArray();

        // Find active bundles that contain at least one plugin from the cart
        return PluginBundle::query()
            ->active()
            ->whereNotIn('id', $cartBundleIds)
            ->whereHas('plugins', function ($query) use ($cartPluginIds) {
                $query->whereIn('plugins.id', $cartPluginIds);
            })
            ->with('plugins')
            ->get();
    }
}
