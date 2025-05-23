<?php

namespace App\Listeners;

use App\Jobs\CreateUserFromStripeCustomer;
use App\Jobs\HandleInvoicePaidJob;
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
        $invoice = Invoice::constructFrom($event->payload['data']['object']);

        dispatch(new HandleInvoicePaidJob($invoice));
    }
}
