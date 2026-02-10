<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_at_addition' => 'integer',
        'bundle_price_at_addition' => 'integer',
        'product_price_at_addition' => 'integer',
    ];

    /**
     * @return BelongsTo<Cart, CartItem>
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * @return BelongsTo<Plugin, CartItem>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @return BelongsTo<PluginPrice, CartItem>
     */
    public function pluginPrice(): BelongsTo
    {
        return $this->belongsTo(PluginPrice::class);
    }

    /**
     * @return BelongsTo<PluginBundle, CartItem>
     */
    public function pluginBundle(): BelongsTo
    {
        return $this->belongsTo(PluginBundle::class);
    }

    /**
     * @return BelongsTo<Product, CartItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isBundle(): bool
    {
        return $this->plugin_bundle_id !== null;
    }

    public function isProduct(): bool
    {
        return $this->product_id !== null;
    }

    public function isPlugin(): bool
    {
        return $this->plugin_id !== null && ! $this->isBundle() && ! $this->isProduct();
    }

    public function getItemPrice(): int
    {
        if ($this->isProduct()) {
            return $this->product_price_at_addition;
        }

        if ($this->isBundle()) {
            return $this->bundle_price_at_addition;
        }

        return $this->price_at_addition;
    }

    public function getFormattedPrice(): string
    {
        return '$'.number_format($this->getItemPrice() / 100);
    }

    public function hasPriceChanged(): bool
    {
        $user = $this->cart->user;

        if ($this->isProduct()) {
            $product = $this->product;

            if (! $product || ! $product->isActive()) {
                return true;
            }

            $currentPrice = $product->getBestPriceForUser($user);

            if (! $currentPrice) {
                return true;
            }

            return $currentPrice->amount !== $this->product_price_at_addition;
        }

        if ($this->isBundle()) {
            $bundle = $this->pluginBundle;

            if (! $bundle || ! $bundle->isActive()) {
                return true;
            }

            $currentPrice = $bundle->getBestPriceForUser($user);

            if (! $currentPrice) {
                return true;
            }

            return $currentPrice->amount !== $this->bundle_price_at_addition;
        }

        $currentPrice = $this->plugin->getBestPriceForUser($user);

        if (! $currentPrice) {
            return true;
        }

        return $currentPrice->amount !== $this->price_at_addition;
    }

    public function getItemName(): string
    {
        if ($this->isProduct()) {
            return $this->product->name;
        }

        if ($this->isBundle()) {
            return $this->pluginBundle->name.' (Bundle)';
        }

        return $this->plugin->name;
    }
}
