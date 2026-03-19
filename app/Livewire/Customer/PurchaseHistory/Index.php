<?php

namespace App\Livewire\Customer\PurchaseHistory;

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Purchase History')]
class Index extends Component
{
    #[Computed]
    public function purchases(): Collection
    {
        $user = auth()->user();

        $licenses = $user->licenses()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($license) {
                return [
                    'type' => 'subscription',
                    'name' => $license->name ?? $license->policy_name,
                    'description' => $license->name ? $license->policy_name : null,
                    'price' => null,
                    'currency' => null,
                    'purchased_at' => $license->created_at,
                    'expires_at' => $license->expires_at,
                    'is_active' => ! $license->is_suspended && (! $license->expires_at || $license->expires_at->isFuture()),
                    'href' => route('customer.licenses.show', $license->key),
                    'is_grandfathered' => false,
                ];
            });

        $pluginLicenses = $user->pluginLicenses()
            ->with('plugin', 'pluginBundle')
            ->orderBy('purchased_at', 'desc')
            ->get()
            ->map(function ($pluginLicense) {
                $name = $pluginLicense->plugin->name ?? 'Plugin';
                $description = null;

                if ($pluginLicense->wasPurchasedAsBundle() && $pluginLicense->pluginBundle) {
                    $description = 'Part of '.$pluginLicense->pluginBundle->name;
                }

                return [
                    'type' => 'plugin',
                    'name' => $name,
                    'description' => $description,
                    'price' => $pluginLicense->price_paid,
                    'currency' => $pluginLicense->currency,
                    'purchased_at' => $pluginLicense->purchased_at,
                    'expires_at' => $pluginLicense->expires_at,
                    'is_active' => $pluginLicense->isActive(),
                    'href' => $pluginLicense->plugin ? route('plugins.show', $pluginLicense->plugin->routeParams()) : null,
                    'is_grandfathered' => $pluginLicense->is_grandfathered,
                ];
            });

        $productLicenses = $user->productLicenses()
            ->with('product')
            ->orderBy('purchased_at', 'desc')
            ->get()
            ->map(function ($productLicense) {
                return [
                    'type' => 'product',
                    'name' => $productLicense->product->name ?? 'Product',
                    'description' => $productLicense->product->description ?? null,
                    'price' => $productLicense->price_paid,
                    'currency' => $productLicense->currency,
                    'purchased_at' => $productLicense->purchased_at,
                    'expires_at' => null,
                    'is_active' => true,
                    'href' => $productLicense->product ? route('products.show', $productLicense->product) : null,
                    'is_grandfathered' => false,
                ];
            });

        return $licenses->concat($pluginLicenses)
            ->concat($productLicenses)
            ->sortByDesc('purchased_at')
            ->values();
    }
}
