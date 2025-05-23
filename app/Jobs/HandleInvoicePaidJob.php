<?php

namespace App\Jobs;

use App\Enums\Subscription;
use App\Exceptions\InvalidStateException;
use App\Models\License;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\SubscriptionItem;
use Stripe\Invoice;
use UnexpectedValueException;

class HandleInvoicePaidJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        match ($this->invoice->billing_reason) {
            Invoice::BILLING_REASON_SUBSCRIPTION_CREATE => $this->createLicense(),
            Invoice::BILLING_REASON_SUBSCRIPTION_UPDATE => null, // TODO: Handle subscription update
            Invoice::BILLING_REASON_SUBSCRIPTION_CYCLE => null, // TODO: Handle subscription renewal
            default => null,
        };
    }

    private function createLicense(): void
    {
        // Assert the invoice line item is for a price_id that relates to a license plan.
        $plan = Subscription::fromStripePriceId($this->invoice->lines->first()->price->id);

        // Assert the invoice line item relates to a subscription and has a subscription item id.
        if (blank($subscriptionItemId = $this->invoice->lines->first()->subscription_item)) {
            throw new UnexpectedValueException('Failed to retrieve the Stripe subscription item id from invoice lines.');
        }

        // Assert we have a subscription item record for this subscription item id.
        $subscriptionItemModel = SubscriptionItem::query()->where('stripe_id', $subscriptionItemId)->firstOrFail();

        // Assert we don't already have an existing license for this subscription item.
        if ($license = License::query()->whereBelongsTo($subscriptionItemModel)->first()) {
            throw new InvalidStateException("A license [{$license->id}] already exists for subscription item [{$subscriptionItemModel->id}].");
        }

        $user = $this->billable();

        dispatch(new CreateAnystackLicenseJob(
            $user,
            $plan,
            $subscriptionItemModel->id,
            $user->first_name,
            $user->last_name,
        ));
    }

    private function billable(): User
    {
        if ($user = Cashier::findBillable($this->invoice->customer)) {
            return $user;
        }

        $customer = Cashier::stripe()->customers->retrieve($this->invoice->customer);

        dispatch_sync(new CreateUserFromStripeCustomer($customer));

        return Cashier::findBillable($this->invoice->customer);
    }
}
