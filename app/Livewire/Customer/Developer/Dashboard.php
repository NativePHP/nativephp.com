<?php

namespace App\Livewire\Customer\Developer;

use App\Services\StripeConnectService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Developer Dashboard')]
class Dashboard extends Component
{
    public function mount(StripeConnectService $stripeConnectService): void
    {
        $developerAccount = auth()->user()->developerAccount;

        if (! $developerAccount || ! $developerAccount->hasCompletedOnboarding()) {
            $this->redirect(route('customer.developer.onboarding'), navigate: true);

            return;
        }

        $stripeConnectService->refreshAccountStatus($developerAccount);
    }

    #[Computed]
    public function developerAccount()
    {
        return auth()->user()->developerAccount;
    }

    #[Computed]
    public function plugins(): Collection
    {
        return auth()->user()->plugins()->withCount('licenses')->get();
    }

    #[Computed]
    public function payouts(): Collection
    {
        return $this->developerAccount
            ->payouts()
            ->with('pluginLicense.plugin')
            ->latest()
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function totalEarnings(): int
    {
        return $this->developerAccount
            ->payouts()
            ->where('status', 'transferred')
            ->sum('developer_amount');
    }

    #[Computed]
    public function pendingEarnings(): int
    {
        return $this->developerAccount
            ->payouts()
            ->where('status', 'pending')
            ->sum('developer_amount');
    }

    public function render()
    {
        return view('livewire.customer.developer.dashboard');
    }
}
