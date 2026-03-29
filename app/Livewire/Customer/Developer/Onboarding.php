<?php

namespace App\Livewire\Customer\Developer;

use App\Support\StripeConnectCountries;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Developer Onboarding')]
class Onboarding extends Component
{
    public string $country = '';

    public string $payoutCurrency = '';

    public function mount(): void
    {
        $developerAccount = auth()->user()->developerAccount;

        if ($developerAccount && $developerAccount->hasCompletedOnboarding()) {
            $this->redirect(route('customer.developer.dashboard'), navigate: true);
        }

        if ($developerAccount) {
            $this->country = $developerAccount->country ?? '';
            $this->payoutCurrency = $developerAccount->payout_currency ?? '';
        }
    }

    public function updatedCountry(string $value): void
    {
        if (StripeConnectCountries::isSupported($value)) {
            $this->payoutCurrency = StripeConnectCountries::defaultCurrency($value);
        } else {
            $this->payoutCurrency = '';
        }
    }

    #[Computed]
    public function developerAccount()
    {
        return auth()->user()->developerAccount;
    }

    #[Computed]
    public function hasExistingAccount(): bool
    {
        return $this->developerAccount !== null;
    }

    /**
     * @return array<string, array{name: string, flag: string, default_currency: string, currencies: list<string>}>
     */
    #[Computed]
    public function countries(): array
    {
        $countries = StripeConnectCountries::all();

        uasort($countries, fn (array $a, array $b) => $a['name'] <=> $b['name']);

        return $countries;
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function availableCurrencies(): array
    {
        if (! $this->country || ! StripeConnectCountries::isSupported($this->country)) {
            return [];
        }

        $currencies = StripeConnectCountries::availableCurrencies($this->country);

        $named = [];
        foreach ($currencies as $code) {
            $named[$code] = StripeConnectCountries::currencyName($code);
        }

        return $named;
    }

    public function render()
    {
        return view('livewire.customer.developer.onboarding');
    }
}
