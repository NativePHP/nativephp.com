<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\StripeClient;

#[Layout('components.layout')]
#[Title('Thank You for Your Purchase')]
class OrderSuccess extends Component
{
    public ?string $email = null;

    public ?string $licenseKey = null;

    public string $checkoutSessionId;

    public function mount(string $checkoutSessionId): void
    {
        $this->checkoutSessionId = $checkoutSessionId;

        $this->loadData();
    }

    public function loadData(): void
    {
        $this->email = $this->loadEmail();
        $this->licenseKey = $this->loadLicenseKey();
    }

    private function loadEmail(): ?string
    {
        if ($email = session('customer_email')) {
            return $email;
        }

        $stripe = app(StripeClient::class);
        $checkoutSession = $stripe->checkout->sessions->retrieve($this->checkoutSessionId);

        if (! ($email = $checkoutSession?->customer_details?->email)) {
            return null;
        }

        session()->put('customer_email', $email);

        return $email;
    }

    private function loadLicenseKey(): ?string
    {
        if ($licenseKey = session('license_key')) {
            return $licenseKey;
        }

        if (! $this->email) {
            return null;
        }

        if ($licenseKey = Cache::get($this->email.'.license_key')) {
            session()->put('license_key', $licenseKey);
        }

        return $licenseKey;
    }
}
