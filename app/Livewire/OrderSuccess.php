<?php

namespace App\Livewire;

use App\Enums\Subscription;
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

    public ?Subscription $subscription = null;

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
        $this->subscription = $this->loadSubscription();
    }

    private function loadEmail(): ?string
    {
        if ($email = session($this->sessionKey('email'))) {
            return $email;
        }

        $stripe = app(StripeClient::class);
        $checkoutSession = $stripe->checkout->sessions->retrieve($this->checkoutSessionId);

        if (! ($email = $checkoutSession?->customer_details?->email)) {
            return null;
        }

        session()->put($this->sessionKey('email'), $email);

        return $email;
    }

    private function loadLicenseKey(): ?string
    {
        if ($licenseKey = session($this->sessionKey('license_key'))) {
            return $licenseKey;
        }

        if (! $this->email) {
            return null;
        }

        if ($licenseKey = Cache::get($this->email.'.license_key')) {
            session()->put($this->sessionKey('license_key'), $licenseKey);
        }

        return $licenseKey;
    }

    private function loadSubscription(): ?Subscription
    {
        if ($subscription = session($this->sessionKey('subscription'))) {
            return Subscription::tryFrom($subscription);
        }

        $stripe = app(StripeClient::class);
        $priceId = $stripe->checkout->sessions->allLineItems($this->checkoutSessionId)->first()?->price->id;

        if (! $priceId) {
            return null;
        }

        $subscription = Subscription::fromStripePriceId($priceId);

        session()->put($this->sessionKey('subscription'), $subscription->value);

        return $subscription;
    }

    private function sessionKey(string $key): string
    {
        return "{$this->checkoutSessionId}.{$key}";
    }
}
