<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Notifications\LicenseExpiryWarning;
use Illuminate\Console\Command;

class SendLicenseExpiryWarnings extends Command
{
    protected $signature = 'licenses:send-expiry-warnings {--catch-up : Send missed warnings for licenses within warning windows}';

    protected $description = 'Send expiry warning emails for licenses that are expiring soon';

    public function handle(): int
    {
        $warningDays = [30, 7, 1];
        $totalSent = 0;
        $catchUp = $this->option('catch-up');

        if ($catchUp) {
            $this->info('Running in catch-up mode - sending missed warnings...');
        }

        foreach ($warningDays as $days) {
            $sent = $this->sendWarningsForDays($days, $catchUp);
            $totalSent += $sent;

            $this->info("Sent {$sent} warning emails for licenses expiring in {$days} day(s)");
        }

        $this->info("Total warning emails sent: {$totalSent}");

        return Command::SUCCESS;
    }

    private function sendWarningsForDays(int $days, bool $catchUp = false): int
    {
        $sent = 0;

        $query = License::query()
            ->whereNull('subscription_item_id') // Legacy licenses without subscriptions
            ->with('user');

        if ($catchUp) {
            // Catch-up mode: find licenses that are within the warning window but haven't received this warning yet
            // For 30-day: expires within 30 days (but more than 7 days to avoid overlap)
            // For 7-day: expires within 7 days (but more than 1 day)
            // For 1-day: expires within 1 day (but hasn't expired yet)
            $warningThresholds = [30 => 7, 7 => 1, 1 => 0];
            $lowerBound = $warningThresholds[$days] ?? 0;

            $query->where('expires_at', '>', now()->addDays($lowerBound)->startOfDay())
                ->where('expires_at', '<=', now()->addDays($days)->endOfDay())
                ->whereDoesntHave('expiryWarnings', function ($q) use ($days) {
                    $q->where('warning_days', $days);
                });
        } else {
            // Normal mode: only licenses expiring on the exact target date
            $targetDate = now()->addDays($days)->startOfDay();
            $query->whereDate('expires_at', $targetDate)
                ->whereDoesntHave('expiryWarnings', function ($q) use ($days) {
                    $q->where('warning_days', $days)
                        ->where('sent_at', '>=', now()->subHours(23)); // Prevent duplicate emails within 23 hours
                });
        }

        $licenses = $query->get();

        foreach ($licenses as $license) {
            if ($license->user) {
                $license->user->notify(new LicenseExpiryWarning($license, $days));

                // Track that we sent this warning
                $license->expiryWarnings()->create([
                    'warning_days' => $days,
                    'sent_at' => now(),
                ]);

                $sent++;

                $this->line("Sent {$days}-day warning to {$license->user->email} for license {$license->key}");
            }
        }

        return $sent;
    }
}
