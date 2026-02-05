<?php

namespace App\Listeners;

use App\Jobs\CreateUserFromStripeCustomer;
use App\Jobs\HandleInvoicePaidJob;
use App\Jobs\RemoveDiscordMaxRoleJob;
use App\Models\User;
use App\Notifications\SubscriptionCancelled;
use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Invoice;

class StripeWebhookReceivedListener
{
    public function handle(WebhookReceived $event): void
    {
        Log::debug('Webhook received', $event->payload);

        match ($event->payload['type']) {
            'invoice.paid' => $this->handleInvoicePaid($event),
            'customer.subscription.created' => $this->createUserIfNotExists($event->payload['data']['object']['customer']),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
            default => null,
        };
    }

    private function createUserIfNotExists(string $stripeCustomerId): void
    {
        if (Cashier::findBillable($stripeCustomerId)) {
            return;
        }

        $customer = Cashier::stripe()->customers->retrieve($stripeCustomerId);

        if (! $customer) {
            throw new Exception(
                'A user needed to be created for customer.subscription.created but was unable to retrieve the customer from Stripe.'
            );
        }

        dispatch_sync(new CreateUserFromStripeCustomer($customer));
    }

    private function handleInvoicePaid(WebhookReceived $event): void
    {
        Log::info('handleInvoicePaid called', [
            'invoice_id' => $event->payload['data']['object']['id'] ?? 'unknown',
            'billing_reason' => $event->payload['data']['object']['billing_reason'] ?? 'unknown',
        ]);

        $invoice = Invoice::constructFrom($event->payload['data']['object']);

        Log::info('Dispatching HandleInvoicePaidJob', ['invoice_id' => $invoice->id]);

        dispatch(new HandleInvoicePaidJob($invoice));

        Log::info('HandleInvoicePaidJob dispatched');
    }

    private function handleSubscriptionDeleted(WebhookReceived $event): void
    {
        $subscription = $event->payload['data']['object'];
        $customerId = $subscription['customer'];

        $user = Cashier::findBillable($customerId);

        if (! $user instanceof User) {
            return;
        }

        $this->removeDiscordRoleIfNoMaxLicense($user);
    }

    private function handleSubscriptionUpdated(WebhookReceived $event): void
    {
        $subscription = $event->payload['data']['object'];
        $customerId = $subscription['customer'];

        $user = Cashier::findBillable($customerId);

        if (! $user instanceof User) {
            return;
        }

        $status = $subscription['status'];
        $previousAttributes = $event->payload['data']['previous_attributes'] ?? [];

        // Send cancellation email if status just changed to canceled
        if ($status === 'canceled' && isset($previousAttributes['status'])) {
            $user->notify(new SubscriptionCancelled);
        }

        if (in_array($status, ['canceled', 'unpaid', 'past_due', 'incomplete_expired'])) {
            $this->removeDiscordRoleIfNoMaxLicense($user);
        }
    }

    private function removeDiscordRoleIfNoMaxLicense(User $user): void
    {
        if (! $user->discord_id) {
            return;
        }

        if ($user->hasMaxAccess()) {
            return;
        }

        dispatch(new RemoveDiscordMaxRoleJob($user));
    }
}
