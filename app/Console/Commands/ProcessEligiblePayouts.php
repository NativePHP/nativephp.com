<?php

namespace App\Console\Commands;

use App\Enums\PayoutStatus;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\DeveloperAccount;
use App\Models\PluginPayout;
use App\Services\StripeConnectService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ProcessEligiblePayouts extends Command
{
    protected $signature = 'payouts:process-eligible';

    protected $description = 'Check Stripe for developers we can\'t pay yet, heal held payouts and dispatch transfer jobs for pending payouts that have passed the 15-day holding period';

    public function handle(StripeConnectService $stripeConnectService): int
    {
        $this->refreshWaitingDeveloperAccounts($stripeConnectService);

        $this->healHeldPayouts();

        $eligiblePayouts = PluginPayout::pending()
            ->where('eligible_for_payout_at', '<=', now())
            ->get();

        if ($eligiblePayouts->isEmpty()) {
            $this->info('No eligible payouts to process.');

            return self::SUCCESS;
        }

        foreach ($eligiblePayouts as $payout) {
            ProcessPayoutTransfer::dispatch($payout);
        }

        $this->info("Dispatched {$eligiblePayouts->count()} payout transfer job(s).");

        return self::SUCCESS;
    }

    /**
     * We only refresh a developer's Stripe status when they visit the site, so check
     * Stripe for anyone we can't pay yet who has payouts waiting on them.
     */
    private function refreshWaitingDeveloperAccounts(StripeConnectService $stripeConnectService): void
    {
        $developerAccounts = DeveloperAccount::query()
            ->whereHas('payouts', fn (Builder $query) => $query->held())
            ->orWhereHas('payouts', fn (Builder $query) => $query->pending()->where('eligible_for_payout_at', '<=', now()))
            ->get()
            ->reject(fn (DeveloperAccount $developerAccount) => $developerAccount->canReceivePayouts());

        foreach ($developerAccounts as $developerAccount) {
            try {
                $stripeConnectService->refreshAccountStatus($developerAccount);
            } catch (\Exception $e) {
                Log::warning('Could not refresh developer account status before processing payouts', [
                    'developer_account_id' => $developerAccount->id,
                    'error' => $e->getMessage(),
                ]);

                $this->warn("Could not check Stripe for developer account #{$developerAccount->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Promote held payouts to pending once the developer's Stripe Connect
     * account is able to receive payouts.
     */
    private function healHeldPayouts(): void
    {
        $heldPayouts = PluginPayout::held()
            ->with('developerAccount')
            ->get();

        $healed = 0;

        foreach ($heldPayouts as $payout) {
            if (! $payout->developerAccount?->canReceivePayouts()) {
                continue;
            }

            $payout->update(['status' => PayoutStatus::Pending]);
            $healed++;

            Log::info('Healed held payout', [
                'payout_id' => $payout->id,
                'developer_account_id' => $payout->developer_account_id,
            ]);
        }

        if ($healed > 0) {
            $this->info("Healed {$healed} held payout(s).");
        }
    }
}
