<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Enums\TeamUserStatus;
use App\Models\Team;
use Flux;
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
        $owner = $this->team->owner;
        $subscription = $owner->subscription();

        if (! $subscription) {
            return;
        }

        // Determine the correct extra seat price based on subscription interval
        $planPriceId = $subscription->stripe_price;

        if (! $planPriceId) {
            foreach ($subscription->items as $item) {
                if (! Subscription::isExtraSeatPrice($item->stripe_price)) {
                    $planPriceId = $item->stripe_price;
                    break;
                }
            }
        }

        $isMonthly = $planPriceId === config('subscriptions.plans.max.stripe_price_id_monthly');
        $interval = $isMonthly ? 'month' : 'year';
        $priceId = Subscription::extraSeatStripePriceId($interval);

        if (! $priceId) {
            return;
        }

        // Check if subscription already has this price item
        $existingItem = $subscription->items->firstWhere('stripe_price', $priceId);

        if ($existingItem) {
            $subscription->incrementAndInvoice($count, $priceId);
        } else {
            $subscription->addPriceAndInvoice($priceId, $count);
        }

        $this->team->increment('extra_seats', $count);
        $this->team->refresh();

        Flux::modal('add-seats')->close();
    }

    public function removeSeats(int $count = 1): void
    {
        if ($this->team->extra_seats < $count) {
            return;
        }

        // Don't allow removing seats if it would go below occupied count
        $newCapacity = $this->team->totalSeatCapacity() - $count;
        if ($newCapacity < $this->team->occupiedSeatCount()) {
            return;
        }

        $owner = $this->team->owner;
        $subscription = $owner->subscription();

        if (! $subscription) {
            return;
        }

        $planPriceId = $subscription->stripe_price;

        if (! $planPriceId) {
            foreach ($subscription->items as $item) {
                if (! Subscription::isExtraSeatPrice($item->stripe_price)) {
                    $planPriceId = $item->stripe_price;
                    break;
                }
            }
        }

        $isMonthly = $planPriceId === config('subscriptions.plans.max.stripe_price_id_monthly');
        $interval = $isMonthly ? 'month' : 'year';
        $priceId = Subscription::extraSeatStripePriceId($interval);

        if (! $priceId) {
            return;
        }

        $existingItem = $subscription->items->firstWhere('stripe_price', $priceId);

        if ($existingItem) {
            if ($existingItem->quantity <= $count) {
                $subscription->removePrice($priceId);
            } else {
                $subscription->decrementQuantity($count, $priceId);
            }
        }

        $this->team->decrement('extra_seats', $count);
        $this->team->refresh();

        Flux::modal('remove-seats')->close();
    }

    public function render()
    {
        $this->team->refresh();
        $this->team->load('users');

        $activeMembers = $this->team->users->where('status', TeamUserStatus::Active);
        $pendingInvitations = $this->team->users->where('status', TeamUserStatus::Pending);

        $extraSeatPriceYearly = config('subscriptions.plans.max.extra_seat_price_yearly', 4);
        $extraSeatPriceMonthly = config('subscriptions.plans.max.extra_seat_price_monthly', 5);

        $removableSeats = min(
            $this->team->extra_seats,
            $this->team->totalSeatCapacity() - $this->team->occupiedSeatCount()
        );

        return view('livewire.team-manager', [
            'activeMembers' => $activeMembers,
            'pendingInvitations' => $pendingInvitations,
            'extraSeatPriceYearly' => $extraSeatPriceYearly,
            'extraSeatPriceMonthly' => $extraSeatPriceMonthly,
            'removableSeats' => max(0, $removableSeats),
        ]);
    }
}
