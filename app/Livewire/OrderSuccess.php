<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Models\License;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\Exception\InvalidRequestException;

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
        try {
            $subscriptionId = Cashier::stripe()->checkout->sessions->retrieve($this->checkoutSessionId)->subscription;
        } catch (InvalidRequestException $e) {
            $this->redirect('/mobile');

            return;
        }

        $subscriptionRecord = Cashier::$subscriptionModel::query()
            ->whereNotNull('stripe_id')
            ->where('stripe_id', $subscriptionId)
            ->first();

        if (! $subscriptionRecord) {
            return;
        }

        $subscriptionItem = Cashier::$subscriptionItemModel::query()
            ->whereBelongsTo($subscriptionRecord)
            ->first();

        if (! $subscriptionItem) {
            report(new ModelNotFoundException("No subscription item found for subscription record [{$subscriptionRecord->id}]."));

            return;
        }

        $this->subscription = Subscription::fromStripePriceId($subscriptionItem->stripe_price);
        $this->email = $subscriptionRecord->user->email;
        $this->licenseKey = License::query()
            ->whereBelongsTo($subscriptionItem)
            ->first()?->key;
    }
}
