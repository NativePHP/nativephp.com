<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected const SESSION_KEY = 'cart_session_id';

    protected const CART_EXPIRY_DAYS = 30;

    public function getCart(?User $user = null): Cart
    {
        if ($user) {
            return $this->getCartForUser($user);
        }

        return $this->getCartForSession();
    }

    protected function getCartForUser(User $user): Cart
    {
        $cart = Cart::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'expires_at' => now()->addDays(self::CART_EXPIRY_DAYS),
            ]);
        }

        return $cart;
    }

    protected function getCartForSession(): Cart
    {
        $sessionId = Session::get(self::SESSION_KEY);

        if ($sessionId) {
            $cart = Cart::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->whereNull('completed_at')
                ->first();

            if ($cart && ! $cart->isExpired()) {
                return $cart;
            }
        }

        $sessionId = Session::getId();
        Session::put(self::SESSION_KEY, $sessionId);

        $cart = Cart::create([
            'session_id' => $sessionId,
            'expires_at' => now()->addDays(self::CART_EXPIRY_DAYS),
        ]);

        return $cart;
    }

    public function addPlugin(Cart $cart, Plugin $plugin): CartItem
    {
        if (! $plugin->is_active) {
            throw new \InvalidArgumentException('This plugin is not available for purchase');
        }

        if (! $plugin->isPaid()) {
            throw new \InvalidArgumentException('Only paid plugins can be added to cart');
        }

        // Get the best price for the cart's user
        $user = $cart->user;
        $bestPrice = $plugin->getBestPriceForUser($user);

        if (! $bestPrice) {
            throw new \InvalidArgumentException('Plugin has no active price');
        }

        if ($cart->hasPlugin($plugin)) {
            return $cart->items()->where('plugin_id', $plugin->id)->first();
        }

        return $cart->items()->create([
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $bestPrice->id,
            'price_at_addition' => $bestPrice->amount,
            'currency' => $bestPrice->currency,
        ]);
    }

    public function removePlugin(Cart $cart, Plugin $plugin): bool
    {
        return $cart->items()->where('plugin_id', $plugin->id)->delete() > 0;
    }

    public function addBundle(Cart $cart, PluginBundle $bundle): CartItem
    {
        if (! $bundle->isActive()) {
            throw new \InvalidArgumentException('Bundle is not available for purchase');
        }

        if ($cart->hasBundle($bundle)) {
            return $cart->items()->where('plugin_bundle_id', $bundle->id)->first();
        }

        // Check if user owns all plugins in the bundle
        $user = $cart->user;
        if ($user && $bundle->isOwnedBy($user)) {
            throw new \InvalidArgumentException('You already own all plugins in this bundle');
        }

        // Get the best price for this user
        $bestPrice = $bundle->getBestPriceForUser($user);
        $priceAmount = $bestPrice ? $bestPrice->amount : $bundle->price;
        $currency = $bestPrice ? $bestPrice->currency : ($bundle->currency ?? 'USD');

        return $cart->items()->create([
            'plugin_bundle_id' => $bundle->id,
            'bundle_price_at_addition' => $priceAmount,
            'currency' => $currency,
        ]);
    }

    public function removeBundle(Cart $cart, PluginBundle $bundle): bool
    {
        return $cart->items()->where('plugin_bundle_id', $bundle->id)->delete() > 0;
    }

    public function transferGuestCartToUser(User $user): ?Cart
    {
        $sessionId = Session::get(self::SESSION_KEY);

        if (! $sessionId) {
            return null;
        }

        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if (! $guestCart || $guestCart->isEmpty()) {
            return null;
        }

        $userCart = Cart::where('user_id', $user->id)->first();

        if ($userCart) {
            // Merge guest cart items into user cart
            foreach ($guestCart->items as $item) {
                if ($item->isBundle()) {
                    if (! $userCart->hasBundle($item->pluginBundle)) {
                        $item->update(['cart_id' => $userCart->id]);
                    }
                } elseif (! $userCart->hasPlugin($item->plugin)) {
                    $item->update(['cart_id' => $userCart->id]);
                }
            }

            $guestCart->delete();
            Session::forget(self::SESSION_KEY);

            return $userCart->fresh();
        }

        // Assign guest cart to user
        $guestCart->assignToUser($user);
        Session::forget(self::SESSION_KEY);

        return $guestCart;
    }

    public function refreshPrices(Cart $cart): array
    {
        $changes = [];

        foreach ($cart->items as $item) {
            if ($item->isBundle()) {
                $bundle = $item->pluginBundle;

                if (! $bundle || ! $bundle->isActive()) {
                    $changes[] = [
                        'item' => $item,
                        'name' => $bundle?->name ?? 'Bundle',
                        'type' => 'unavailable',
                        'old_price' => $item->bundle_price_at_addition,
                    ];
                    $item->delete();

                    continue;
                }

                // Get the best price for the cart's user
                $user = $cart->user;
                $bestPrice = $bundle->getBestPriceForUser($user);
                $currentPrice = $bestPrice ? $bestPrice->amount : $bundle->price;
                $currency = $bestPrice ? $bestPrice->currency : ($bundle->currency ?? 'USD');

                if ($currentPrice !== $item->bundle_price_at_addition) {
                    $changes[] = [
                        'item' => $item,
                        'name' => $bundle->name,
                        'type' => 'price_changed',
                        'old_price' => $item->bundle_price_at_addition,
                        'new_price' => $currentPrice,
                    ];

                    $item->update([
                        'bundle_price_at_addition' => $currentPrice,
                        'currency' => $currency,
                    ]);
                }
            } else {
                $plugin = $item->plugin;

                // Remove inactive plugins
                if (! $plugin || ! $plugin->is_active) {
                    $changes[] = [
                        'item' => $item,
                        'name' => $plugin?->name ?? 'Plugin',
                        'type' => 'unavailable',
                        'old_price' => $item->price_at_addition,
                    ];
                    $item->delete();

                    continue;
                }

                // Get the best price for the cart's user
                $user = $cart->user;
                $currentPrice = $plugin->getBestPriceForUser($user);

                if (! $currentPrice) {
                    $changes[] = [
                        'item' => $item,
                        'name' => $plugin->name,
                        'type' => 'unavailable',
                        'old_price' => $item->price_at_addition,
                    ];
                    $item->delete();

                    continue;
                }

                // Check if price has changed (compare amounts)
                if ($currentPrice->amount === $item->price_at_addition) {
                    continue;
                }

                $changes[] = [
                    'item' => $item,
                    'name' => $item->plugin->name,
                    'type' => 'price_changed',
                    'old_price' => $item->price_at_addition,
                    'new_price' => $currentPrice->amount,
                ];

                $item->update([
                    'plugin_price_id' => $currentPrice->id,
                    'price_at_addition' => $currentPrice->amount,
                    'currency' => $currentPrice->currency,
                ]);
            }
        }

        return $changes;
    }

    public function removeAlreadyOwned(Cart $cart, User $user): array
    {
        $removed = [];

        foreach ($cart->items as $item) {
            if ($item->isBundle()) {
                $bundle = $item->pluginBundle;
                if ($bundle && $bundle->isOwnedBy($user)) {
                    $removed[] = ['type' => 'bundle', 'item' => $bundle];
                    $item->delete();
                }
            } elseif ($user->hasPluginAccess($item->plugin)) {
                $removed[] = ['type' => 'plugin', 'item' => $item->plugin];
                $item->delete();
            }
        }

        return $removed;
    }

    public function getCartItemCount(?User $user = null): int
    {
        if ($user) {
            $cart = Cart::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->first();
        } else {
            $sessionId = Session::get(self::SESSION_KEY);
            $cart = $sessionId
                ? Cart::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->whereNull('completed_at')
                    ->first()
                : null;
        }

        return $cart ? $cart->itemCount() : 0;
    }

    /**
     * Exchange individual plugins in the cart for a bundle.
     */
    public function exchangeForBundle(Cart $cart, PluginBundle $bundle): CartItem
    {
        if (! $bundle->isActive()) {
            throw new \InvalidArgumentException('Bundle is not available for purchase');
        }

        if ($cart->hasBundle($bundle)) {
            throw new \InvalidArgumentException('Bundle is already in your cart');
        }

        // Check if user owns all plugins in the bundle
        $user = $cart->user;
        if ($user && $bundle->isOwnedBy($user)) {
            throw new \InvalidArgumentException('You already own all plugins in this bundle');
        }

        // Get the plugin IDs in this bundle
        $bundlePluginIds = $bundle->plugins->pluck('id')->toArray();

        // Remove individual plugin items that are in the bundle
        $cart->items()
            ->whereIn('plugin_id', $bundlePluginIds)
            ->delete();

        // Get the best price for this user
        $bestPrice = $bundle->getBestPriceForUser($user);
        $priceAmount = $bestPrice ? $bestPrice->amount : $bundle->price;
        $currency = $bestPrice ? $bestPrice->currency : ($bundle->currency ?? 'USD');

        // Add the bundle
        return $cart->items()->create([
            'plugin_bundle_id' => $bundle->id,
            'bundle_price_at_addition' => $priceAmount,
            'currency' => $currency,
        ]);
    }
}
