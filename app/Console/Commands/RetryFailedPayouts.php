<?php

namespace App\Console\Commands;

use App\Enums\PayoutStatus;
use App\Models\PluginPayout;
use App\Services\StripeConnectService;
use Illuminate\Console\Command;

class RetryFailedPayouts extends Command
{
    protected $signature = 'payouts:retry-failed {--payout-id= : Retry a specific payout}';

    protected $description = 'Retry failed plugin payouts';

    public function handle(StripeConnectService $stripeConnectService): int
    {
        $payoutId = $this->option('payout-id');

        if ($payoutId) {
            $payout = PluginPayout::find($payoutId);

            if (! $payout) {
                $this->error("Payout #{$payoutId} not found.");

                return self::FAILURE;
            }

            if (! $payout->isFailed()) {
                $this->error("Payout #{$payoutId} is not in failed status.");

                return self::FAILURE;
            }

            return $this->retryPayout($payout, $stripeConnectService);
        }

        $failedPayouts = PluginPayout::failed()
            ->with(['pluginLicense', 'developerAccount'])
            ->get();

        if ($failedPayouts->isEmpty()) {
            $this->info('No failed payouts to retry.');

            return self::SUCCESS;
        }

        $this->info("Found {$failedPayouts->count()} failed payout(s) to retry.");

        $succeeded = 0;
        $failed = 0;

        foreach ($failedPayouts as $payout) {
            // Reset status to pending before retrying
            $payout->update(['status' => PayoutStatus::Pending]);

            if ($stripeConnectService->processTransfer($payout)) {
                $this->info("Payout #{$payout->id} succeeded.");
                $succeeded++;
            } else {
                $this->error("Payout #{$payout->id} failed again.");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Results: {$succeeded} succeeded, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function retryPayout(PluginPayout $payout, StripeConnectService $stripeConnectService): int
    {
        $this->info("Retrying payout #{$payout->id}...");

        // Reset status to pending before retrying
        $payout->update(['status' => PayoutStatus::Pending]);

        if ($stripeConnectService->processTransfer($payout)) {
            $this->info('Payout succeeded!');

            return self::SUCCESS;
        }

        $this->error('Payout failed again.');

        return self::FAILURE;
    }
}
