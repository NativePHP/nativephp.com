<?php

namespace App\Livewire\Customer\PurchasedPlugins;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Purchased Plugins')]
class Index extends Component
{
    #[Computed]
    public function pluginLicenses(): Collection
    {
        return auth()->user()->pluginLicenses()
            ->with('plugin', 'pluginBundle')
            ->orderBy('purchased_at', 'desc')
            ->get();
    }

    #[Computed]
    public function pluginLicenseKey(): string
    {
        return auth()->user()->getPluginLicenseKey();
    }

    #[Computed]
    public function teamPlugins(): SupportCollection
    {
        $membership = auth()->user()->activeTeamMembership();

        if (! $membership) {
            return collect();
        }

        return $membership->team->owner->pluginLicenses()
            ->active()
            ->with('plugin')
            ->get()
            ->pluck('plugin')
            ->filter()
            ->unique('id');
    }

    #[Computed]
    public function teamOwnerName(): ?string
    {
        $membership = auth()->user()->activeTeamMembership();

        return $membership?->team->owner->display_name;
    }

    public function rotateKey(): void
    {
        auth()->user()->regeneratePluginLicenseKey();

        unset($this->pluginLicenseKey);

        session()->flash('success', 'Your plugin license key has been rotated. Please update your Composer configuration with the new key.');
    }
}
