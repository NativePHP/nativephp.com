<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Notifications\LicenseExpiryWarning;
use Illuminate\Console\Command;

class SendLicenseExpiryWarnings extends Command
{
    protected $signature = 'licenses:send-expiry-warnings';

    protected $description = 'Send expiry warning emails for licenses that are expiring soon';

    public function handle(): int
    {
        $warningDays = [30, 7, 1];
        $totalSent = 0;

        foreach ($warningDays as $days) {
            $sent = $this->sendWarningsForDays($days);
            $totalSent += $sent;

            $this->info("Sent {$sent} warning emails for licenses expiring in {$days} day(s)");
        }

        $this->info("Total warning emails sent: {$totalSent}");

        return Command::SUCCESS;
    }

    private function sendWarningsForDays(int $days): int
    {
        $targetDate = now()->addDays($days)->startOfDay();
        $sent = 0;

        // Find licenses that:
        // 1. Expire on the target date
        // 2. Don't have an active subscription (legacy licenses)
        // 3. Haven't been sent a warning for this specific day count recently
        $licenses = License::query()
            ->whereDate('expires_at', $targetDate)
            ->whereNull('subscription_item_id') // Legacy licenses without subscriptions
            ->whereDoesntHave('expiryWarnings', function ($query) use ($days) {
                $query->where('warning_days', $days)
                    ->where('sent_at', '>=', now()->subHours(23)); // Prevent duplicate emails within 23 hours
            })
            ->with('user')
            ->get();

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
