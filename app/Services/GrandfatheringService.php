<?php

namespace App\Services;

use App\Enums\GrandfatheringTier;
use App\Models\User;
use App\Models\UserPurchaseHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GrandfatheringService
{
    public const CUTOFF_DATE = '2025-06-01 00:00:00';

    public const HIGH_SPEND_THRESHOLD = 25000;

    public function determineUserTier(User $user): GrandfatheringTier
    {
        $purchaseHistory = $user->purchaseHistory;

        if (! $purchaseHistory || ! $purchaseHistory->hasPurchaseHistory()) {
            return GrandfatheringTier::None;
        }

        $firstPurchaseDate = $purchaseHistory->first_purchase_at;
        $totalSpent = $purchaseHistory->total_spent;

        $isPostCutoff = $firstPurchaseDate->isAfter(Carbon::parse(self::CUTOFF_DATE));

        if ($isPostCutoff && $totalSpent >= self::HIGH_SPEND_THRESHOLD) {
            return GrandfatheringTier::FreeOfficialPlugins;
        }

        if ($isPostCutoff || ! $isPostCutoff) {
            return GrandfatheringTier::Discounted;
        }

        return GrandfatheringTier::None;
    }

    public function recalculateForUser(User $user): UserPurchaseHistory
    {
        $purchaseData = $this->calculatePurchaseData($user);

        $purchaseHistory = $user->purchaseHistory ?? new UserPurchaseHistory(['user_id' => $user->id]);

        $purchaseHistory->fill([
            'total_spent' => $purchaseData['total_spent'],
            'first_purchase_at' => $purchaseData['first_purchase_at'],
            'recalculated_at' => now(),
        ]);

        $purchaseHistory->grandfathering_tier = $this->determineUserTierFromData($purchaseData);

        $purchaseHistory->save();

        Log::info("Recalculated grandfathering for user {$user->id}", [
            'tier' => $purchaseHistory->grandfathering_tier->value,
            'total_spent' => $purchaseHistory->total_spent,
            'first_purchase_at' => $purchaseHistory->first_purchase_at?->toIso8601String(),
        ]);

        return $purchaseHistory;
    }

    /**
     * @return array{total_spent: int, first_purchase_at: ?Carbon}
     */
    protected function calculatePurchaseData(User $user): array
    {
        $totalSpent = 0;
        $firstPurchaseAt = null;

        $licenses = $user->licenses()->orderBy('created_at', 'asc')->get();

        foreach ($licenses as $license) {
            if ($license->subscription_item_id && $license->subscriptionItem) {
                $subscriptionItem = $license->subscriptionItem;
                $subscription = $subscriptionItem->subscription;

                if ($subscription && $subscription->stripe_price) {
                    $totalSpent += $this->getAmountFromStripe($user, $subscription);
                }
            }

            if ($firstPurchaseAt === null) {
                $firstPurchaseAt = $license->created_at;
            }
        }

        if ($totalSpent === 0 && $user->stripe_id) {
            $stripeData = $this->calculateFromStripeInvoices($user);
            $totalSpent = $stripeData['total_spent'];
            $firstPurchaseAt = $stripeData['first_purchase_at'] ?? $firstPurchaseAt;
        }

        return [
            'total_spent' => $totalSpent,
            'first_purchase_at' => $firstPurchaseAt,
        ];
    }

    /**
     * @param  array{total_spent: int, first_purchase_at: ?Carbon}  $data
     */
    protected function determineUserTierFromData(array $data): GrandfatheringTier
    {
        if (! $data['first_purchase_at']) {
            return GrandfatheringTier::None;
        }

        $isPostCutoff = $data['first_purchase_at']->isAfter(Carbon::parse(self::CUTOFF_DATE));

        if ($isPostCutoff && $data['total_spent'] >= self::HIGH_SPEND_THRESHOLD) {
            return GrandfatheringTier::FreeOfficialPlugins;
        }

        return GrandfatheringTier::Discounted;
    }

    protected function getAmountFromStripe(User $user, $subscription): int
    {
        try {
            $invoices = $user->invoices();

            foreach ($invoices as $invoice) {
                if ($invoice->subscription === $subscription->stripe_id) {
                    return $invoice->rawTotal();
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get Stripe invoice data for user {$user->id}: {$e->getMessage()}");
        }

        return 0;
    }

    /**
     * @return array{total_spent: int, first_purchase_at: ?Carbon}
     */
    protected function calculateFromStripeInvoices(User $user): array
    {
        $totalSpent = 0;
        $firstPurchaseAt = null;

        try {
            $invoices = $user->invoices();

            foreach ($invoices as $invoice) {
                if ($invoice->paid) {
                    $totalSpent += $invoice->rawTotal();

                    $invoiceDate = Carbon::createFromTimestamp($invoice->created);
                    if ($firstPurchaseAt === null || $invoiceDate->lt($firstPurchaseAt)) {
                        $firstPurchaseAt = $invoiceDate;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to calculate from Stripe invoices for user {$user->id}: {$e->getMessage()}");
        }

        return [
            'total_spent' => $totalSpent,
            'first_purchase_at' => $firstPurchaseAt,
        ];
    }

    public function getApplicableDiscount(User $user, bool $isOfficialPlugin): int
    {
        $tier = $this->determineUserTier($user);

        if ($tier === GrandfatheringTier::FreeOfficialPlugins && $isOfficialPlugin) {
            return 100;
        }

        if ($tier === GrandfatheringTier::Discounted) {
            return $tier->getDiscountPercent();
        }

        return 0;
    }
}
