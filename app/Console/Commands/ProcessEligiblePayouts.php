<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPayoutTransfer;
use App\Models\PluginPayout;
use Illuminate\Console\Command;

class ProcessEligiblePayouts extends Command
{
    protected $signature = 'payouts:process-eligible';

    protected $description = 'Dispatch transfer jobs for pending payouts that have passed the 15-day holding period';

    public function handle(): int
    {
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
}
