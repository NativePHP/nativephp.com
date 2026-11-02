<?php

namespace App\Actions;

use App\Models\PluginLicense;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Stripe\LineItem;

class RefundPluginPurchase
{
    public function __construct(private StripeConnectService $stripeConnectService) {}

    /**
     * Refund a plugin purchase, revoking the license and cancelling/reversing the payout.
     *
     * Only the license's own line of the Stripe checkout is refunded, at what the buyer paid
     * for it after coupons and tax, so anything else bought in the same checkout is left alone.
     *
     * For bundle purchases, all sibling licenses sharing the same stripe_payment_intent_id
     * are refunded together. A bundle is one line of the checkout, so that is one refund.
     *
     * @return int The amount refunded, in cents.
     */
    public function handle(PluginLicense $license, User $refundedBy): int
    {
        if (! $license->isRefundable()) {
            throw new \RuntimeException('This license is not eligible for a refund.');
        }

        $licenses = $this->collectLicensesToRefund($license);

        $lineItem = $this->refundableLineItem($license);

        $refund = $this->stripeConnectService->refundCheckoutLineItem($license->stripe_payment_intent_id, $lineItem);

        DB::transaction(function () use ($licenses, $refund, $refundedBy): void {
            foreach ($licenses as $licenseToRefund) {
                $licenseToRefund->update([
                    'refunded_at' => now(),
                    'stripe_refund_id' => $refund->id,
                    'refunded_by' => $refundedBy->id,
                ]);

                $payout = $licenseToRefund->payout;

                if (! $payout || $payout->isCancelled()) {
                    continue;
                }

                if ($payout->isTransferred()) {
                    $this->stripeConnectService->reverseTransfer($payout->stripe_transfer_id);
                }

                $payout->markAsCancelled();
            }
        });

        return $lineItem->amount_total;
    }

    /**
     * @return Collection<int, PluginLicense>
     */
    private function collectLicensesToRefund(PluginLicense $license): Collection
    {
        if (! $license->wasPurchasedAsBundle()) {
            return collect([$license]);
        }

        return PluginLicense::query()
            ->where('stripe_payment_intent_id', $license->stripe_payment_intent_id)
            ->where('plugin_bundle_id', $license->plugin_bundle_id)
            ->get();
    }

    /**
     * The line of the license's Stripe checkout to refund, which holds what the buyer paid for it.
     */
    private function refundableLineItem(PluginLicense $license): LineItem
    {
        $lineItem = $this->findCheckoutLineItem($license);

        if (! $lineItem) {
            throw new \RuntimeException('Could not find this license on its Stripe checkout, so there is no amount to refund.');
        }

        if ($lineItem->amount_total <= 0) {
            throw new \RuntimeException('Nothing was charged for this license, so there is nothing to refund.');
        }

        return $lineItem;
    }

    /**
     * Find the line of the license's Stripe checkout that it was bought on.
     *
     * Lines are tagged with the ID of the plugin or bundle they are for. Checkouts created
     * before that tagging have no metadata, so those are matched on the name the checkout
     * gave the line instead. A line that matches neither way is never used.
     */
    private function findCheckoutLineItem(PluginLicense $license): ?LineItem
    {
        $lineItems = $this->stripeConnectService->checkoutLineItems($license->stripe_payment_intent_id);

        [$metadataKey, $id, $name] = $license->wasPurchasedAsBundle()
            ? ['plugin_bundle_id', $license->plugin_bundle_id, $license->pluginBundle->name.' (Bundle)']
            : ['plugin_id', $license->plugin_id, $license->plugin->name];

        $taggedLineItem = $lineItems->first(
            fn (LineItem $lineItem): bool => (string) ($lineItem->price->product->metadata[$metadataKey] ?? '') === (string) $id
        );

        return $taggedLineItem ?? $lineItems->first(fn (LineItem $lineItem): bool => $lineItem->description === $name);
    }
}
