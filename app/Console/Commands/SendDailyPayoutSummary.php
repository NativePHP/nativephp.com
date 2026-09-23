<?php

namespace App\Console\Commands;

use App\Enums\PayoutStatus;
use App\Models\PluginPayout;
use App\Notifications\DailyPayoutSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDailyPayoutSummary extends Command
{
    protected $signature = 'payouts:send-daily-summary';

    protected $description = 'Email a summary of the plugin payouts sent in the last 24 hours and the ones still waiting';

    public function handle(): int
    {
        $attemptedPayouts = PluginPayout::query()
            ->whereIn('status', [PayoutStatus::Transferred, PayoutStatus::Failed])
            ->where('last_attempted_at', '>=', now()->subDay())
            ->with(['developerAccount.user', 'pluginLicense.plugin'])
            ->oldest('id')
            ->get();

        [$upcomingPayouts, $pendingPayouts] = PluginPayout::query()
            ->whereIn('status', [PayoutStatus::Pending, PayoutStatus::Held])
            ->with('developerAccount')
            ->oldest('id')
            ->get()
            ->partition(fn (PluginPayout $payout): bool => $payout->isPending() && $payout->developerAccount?->canReceivePayouts());

        if ($attemptedPayouts->isEmpty() && $upcomingPayouts->isEmpty() && $pendingPayouts->isEmpty()) {
            $this->info('No payouts to report.');

            return self::SUCCESS;
        }

        Notification::route('mail', config('mail.support_address'))
            ->notify(new DailyPayoutSummary(
                attemptedPayouts: $attemptedPayouts,
                upcomingPayouts: $upcomingPayouts,
                pendingPayouts: $pendingPayouts,
                totalPaidOut: (int) PluginPayout::transferred()->sum('developer_amount'),
            ));

        $this->info('Sent the daily payout summary.');

        return self::SUCCESS;
    }
}
