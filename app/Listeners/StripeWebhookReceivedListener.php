<?php

namespace App\Listeners;

use App\Jobs\CreateUserFromStripeCustomer;
use App\Jobs\HandleInvoicePaidJob;
use App\Jobs\ProcessPluginCheckoutJob;
use App\Jobs\RemoveDiscordMaxRoleJob;
use App\Models\User;
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
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event),
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

    private function handleCheckoutSessionCompleted(WebhookReceived $event): void
    {
        $session = $event->payload['data']['object'];

        // Only process completed payment sessions for plugins
        if ($session['payment_status'] !== 'paid') {
            return;
        }

        $metadata = $session['metadata'] ?? [];

        // Only process if this is a plugin purchase (has plugin_id or plugin_ids in metadata)
        if (! isset($metadata['plugin_id']) && ! isset($metadata['plugin_ids'])) {
            return;
        }

        Log::info('Dispatching ProcessPluginCheckoutJob from webhook', [
            'session_id' => $session['id'],
            'metadata' => $metadata,
            'has_cart_id' => isset($metadata['cart_id']),
            'has_plugin_ids' => isset($metadata['plugin_ids']),
            'plugin_ids_value' => $metadata['plugin_ids'] ?? null,
        ]);

        dispatch(new ProcessPluginCheckoutJob(
            checkoutSessionId: $session['id'],
            metadata: $metadata,
            amountTotal: $session['amount_total'],
            currency: $session['currency'],
            paymentIntentId: $session['payment_intent'] ?? null,
        ));
    }
}
