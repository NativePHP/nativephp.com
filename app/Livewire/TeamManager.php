<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Enums\TeamUserStatus;
use App\Models\Team;
use Carbon\Carbon;
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

        $owner = $this->team->owner;
        $subscription = $owner->subscription();
        $planPriceId = $subscription?->stripe_price;

        if ($subscription && ! $planPriceId) {
            foreach ($subscription->items as $item) {
                if (! Subscription::isExtraSeatPrice($item->stripe_price)) {
                    $planPriceId = $item->stripe_price;
                    break;
                }
            }
        }

        $isMonthly = $planPriceId === config('subscriptions.plans.max.stripe_price_id_monthly');
        $extraSeatPrice = $isMonthly
            ? config('subscriptions.plans.max.extra_seat_price_monthly', 5)
            : config('subscriptions.plans.max.extra_seat_price_yearly', 4) * 12;
        $extraSeatInterval = $isMonthly ? 'mo' : 'yr';

        // Calculate pro-rata fraction and billing summary from Stripe
        $proRataFraction = 1.0;
        $renewalDate = null;
        $planPrice = null;
        $seatsCost = null;
        $extraSeatsQty = 0;
        $nextBillTotal = null;
        $billingInterval = $isMonthly ? 'mo' : 'yr';

        if ($subscription) {
            try {
                $stripeSubscription = $subscription->asStripeSubscription();
                $periodStart = Carbon::createFromTimestamp($stripeSubscription->current_period_start);
                $periodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);
                $totalDays = $periodStart->diffInDays($periodEnd);
                $remainingDays = now()->diffInDays($periodEnd, false);

                if ($totalDays > 0 && $remainingDays > 0) {
                    $proRataFraction = round($remainingDays / $totalDays, 4);
                }

                $renewalDate = $periodEnd->format('M j, Y');

                // Extract billing amounts from Stripe subscription items
                foreach ($stripeSubscription->items->data as $item) {
                    $unitAmount = $item->price->unit_amount / 100;
                    $qty = $item->quantity ?? 1;

                    if (Subscription::isExtraSeatPrice($item->price->id)) {
                        $seatsCost = $unitAmount * $qty;
                        $extraSeatsQty = $qty;
                    } else {
                        $planPrice = $unitAmount;
                    }
                }

                $nextBillTotal = ($planPrice ?? 0) + ($seatsCost ?? 0);
            } catch (\Exception) {
                // Fall back to showing full price without pro-rata
            }
        }

        $removableSeats = min(
            $this->team->extra_seats,
            $this->team->totalSeatCapacity() - $this->team->occupiedSeatCount()
        );

        return view('livewire.team-manager', [
            'activeMembers' => $activeMembers,
            'pendingInvitations' => $pendingInvitations,
            'extraSeatPrice' => $extraSeatPrice,
            'extraSeatInterval' => $extraSeatInterval,
            'proRataFraction' => $proRataFraction,
            'renewalDate' => $renewalDate,
            'removableSeats' => max(0, $removableSeats),
            'planPrice' => $planPrice,
            'seatsCost' => $seatsCost,
            'extraSeatsQty' => $extraSeatsQty,
            'nextBillTotal' => $nextBillTotal,
            'billingInterval' => $billingInterval,
        ]);
    }
}
