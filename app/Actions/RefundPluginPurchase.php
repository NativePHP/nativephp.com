<?php

namespace App\Actions;

use App\Models\PluginLicense;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RefundPluginPurchase
{
    public function __construct(private StripeConnectService $stripeConnectService) {}

    /**
     * Refund a plugin purchase, revoking the license and cancelling/reversing the payout.
     *
     * For bundle purchases, all sibling licenses sharing the same stripe_payment_intent_id
     * are refunded together.
     */
    public function handle(PluginLicense $license, User $refundedBy): void
    {
        if (! $license->isRefundable()) {
            throw new \RuntimeException('This license is not eligible for a refund.');
        }

        $licenses = $this->collectLicensesToRefund($license);

        $refund = $this->stripeConnectService->refundPaymentIntent($license->stripe_payment_intent_id);

        DB::transaction(function () use ($licenses, $refund, $refundedBy): void {
            foreach ($licenses as $licenseToRefund) {
                $licenseToRefund->update([
                    'refunded_at' => now(),
                    'stripe_refund_id' => $refund->id,
                    'refunded_by' => $refundedBy->id,
                ]);

                $payout = $licenseToRefund->payout;

                if (! $payout) {
                    continue;
                }

                if ($payout->isPending()) {
                    $payout->markAsCancelled();
                } elseif ($payout->isTransferred()) {
                    $this->stripeConnectService->reverseTransfer($payout->stripe_transfer_id);
                    $payout->markAsCancelled();
                }
            }
        });
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
}
