<?php

namespace App\Console\Commands;

use App\Enums\PayoutStatus;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\PluginPayout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessEligiblePayouts extends Command
{
    protected $signature = 'payouts:process-eligible';

    protected $description = 'Heal held payouts and dispatch transfer jobs for pending payouts that have passed the 15-day holding period';

    public function handle(): int
    {
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
