<?php

namespace App\Livewire\Customer;

use App\Enums\Subscription;
use App\Models\Team;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    #[Computed]
    public function licenseCount(): int
    {
        return auth()->user()->licenses()->count();
    }

    #[Computed]
    public function isEapCustomer(): bool
    {
        return auth()->user()->isEapCustomer();
    }

    #[Computed]
    public function activeSubscription()
    {
        return auth()->user()->subscription();
    }

    #[Computed]
    public function subscriptionName(): ?string
    {
        $subscription = $this->activeSubscription;

        if (! $subscription) {
            return null;
        }

        if ($subscription->stripe_price) {
            try {
                return Subscription::fromStripePriceId($subscription->stripe_price)->name();
            } catch (\RuntimeException) {
                return ucfirst($subscription->type);
            }
        }

        return ucfirst($subscription->type);
    }

    #[Computed]
    public function hasUltraSubscription(): bool
    {
        return auth()->user()->hasActiveUltraSubscription();
    }

    #[Computed]
    public function ownedTeam(): ?Team
    {
        return auth()->user()->ownedTeam;
    }

    #[Computed]
    public function teamMemberCount(): int
    {
        return $this->ownedTeam?->activeUserCount() ?? 0;
    }

    #[Computed]
    public function pluginLicenseCount(): int
    {
        return auth()->user()->pluginLicenses()->count();
    }

    #[Computed]
    public function renewalLicenseKey(): ?string
    {
        if ($this->activeSubscription) {
            return null;
        }

        $highestTierLicense = auth()->user()->licenses()
            ->whereIn('policy_name', ['max', 'pro', 'mini'])
            ->orderByRaw("CASE policy_name WHEN 'max' THEN 1 WHEN 'pro' THEN 2 WHEN 'mini' THEN 3 END")
            ->first();

        return $highestTierLicense?->key;
    }

    #[Computed]
    public function connectedAccountsCount(): int
    {
        $user = auth()->user();

        return ($user->hasGitHubToken() ? 1 : 0) + ($user->hasDiscordConnected() ? 1 : 0);
    }

    #[Computed]
    public function connectedAccountsDescription(): string
    {
        $user = auth()->user();
        $hasGitHub = $user->hasGitHubToken();
        $hasDiscord = $user->hasDiscordConnected();

        return match (true) {
            $hasGitHub && $hasDiscord => 'GitHub & Discord',
            $hasGitHub => 'GitHub connected',
            $hasDiscord => 'Discord connected',
            default => 'No accounts connected',
        };
    }

    #[Computed]
    public function totalPurchases(): int
    {
        $user = auth()->user();

        return $this->licenseCount
            + $this->pluginLicenseCount
            + $user->productLicenses()->count();
    }
}
