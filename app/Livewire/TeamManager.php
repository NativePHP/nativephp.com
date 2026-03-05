<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Models\Team;
use Livewire\Component;

class TeamManager extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function addSeats(int $count = 1): void
    {
        $count = max(1, $count);

        $user = $this->team->owner;

        if (! $user->hasUltraAccess()) {
            $this->addError('seats', 'A paying Ultra subscription is required to manage seats.');

            return;
        }

        $subscription = $user->subscription();

        $priceId = $this->resolveExtraSeatPriceId($subscription);

        if (! $priceId) {
            $this->addError('seats', 'Unable to determine seat pricing for your subscription interval.');

            return;
        }

        $existingItem = $this->findExtraSeatItem($subscription);

        if ($existingItem) {
            $subscription->incrementAndInvoice($count, $priceId);
        } else {
            $subscription->addPriceAndInvoice($priceId, $count);
        }

        $this->team->increment('extra_seats', $count);
        $this->team->refresh();

        $this->dispatch('seats-updated');
    }

    public function removeSeats(int $count = 1): void
    {
        $count = max(1, min($count, $this->team->extra_seats, $this->team->availableSeats()));

        if (! $this->team->canRemoveExtraSeats($count)) {
            $this->addError('seats', 'Cannot remove seats while they are occupied by team members.');

            return;
        }

        $user = $this->team->owner;

        if (! $user->hasUltraAccess()) {
            $this->addError('seats', 'A paying Ultra subscription is required to manage seats.');

            return;
        }

        $subscription = $user->subscription();

        $priceId = $this->resolveExtraSeatPriceId($subscription);

        if (! $priceId) {
            $this->addError('seats', 'Unable to determine seat pricing.');

            return;
        }

        $existingItem = $this->findExtraSeatItem($subscription);

        if (! $existingItem) {
            $this->addError('seats', 'No extra seat subscription item found.');

            return;
        }

        $newQuantity = $this->team->extra_seats - $count;

        if ($newQuantity <= 0) {
            $subscription->removePrice($priceId);
        } else {
            $subscription->decrementQuantity($count, $priceId);
        }

        $this->team->decrement('extra_seats', $count);
        $this->team->refresh();

        $this->dispatch('seats-updated');
    }

    public function render()
    {
        $user = $this->team->owner;
        $subscription = $user->subscription();
        $billingInterval = null;
        $extraSeatPrice = null;

        if ($subscription && $subscription->active()) {
            $billingInterval = $this->detectBillingInterval($subscription);
            $extraSeatPrice = match ($billingInterval) {
                'month' => config('subscriptions.plans.max.extra_seat_price_monthly'),
                'year' => config('subscriptions.plans.max.extra_seat_price_yearly'),
                default => null,
            };
        }

        return view('livewire.team-manager', [
            'extraSeatPrice' => $extraSeatPrice,
            'billingInterval' => $billingInterval,
        ]);
    }

    private function resolveExtraSeatPriceId($subscription): ?string
    {
        $interval = $this->detectBillingInterval($subscription);

        return Subscription::extraSeatStripePriceId($interval);
    }

    private function findExtraSeatItem($subscription): ?\Laravel\Cashier\SubscriptionItem
    {
        foreach ($subscription->items as $item) {
            if (Subscription::isExtraSeatPrice($item->stripe_price)) {
                return $item;
            }
        }

        return null;
    }

    private function detectBillingInterval($subscription): ?string
    {
        // Check the plan subscription item's interval
        foreach ($subscription->items as $item) {
            if (! Subscription::isExtraSeatPrice($item->stripe_price)) {
                // Use stripe API to check the price interval
                $yearlyPrices = array_filter([
                    config('subscriptions.plans.max.stripe_price_id'),
                    config('subscriptions.plans.max.stripe_price_id_eap'),
                    config('subscriptions.plans.max.stripe_price_id_discounted'),
                ]);

                if (in_array($item->stripe_price, $yearlyPrices)) {
                    return 'year';
                }

                return 'month';
            }
        }

        return null;
    }
}
