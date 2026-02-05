<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Notifications\LegacyLicenseThankYou;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLegacyLicenseThankYou extends Command
{
    protected $signature = 'licenses:send-legacy-thank-you
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send a one-time thank you email to legacy license holders who have not renewed';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No emails will be sent');
        }

        // Find all legacy licenses (no subscription, created before May 8, 2025)
        // that have a user and haven't been converted to a subscription
        $legacyLicenses = License::query()
            ->whereNull('subscription_item_id')
            ->where('created_at', '<', Carbon::create(2025, 5, 8))
            ->whereHas('user')
            ->with('user')
            ->get();

        $this->info("Found {$legacyLicenses->count()} legacy license(s)");

        // Group by user to avoid sending multiple emails to the same person
        $userLicenses = $legacyLicenses->groupBy('user_id');

        $sent = 0;
        $skipped = 0;

        foreach ($userLicenses as $userId => $licenses) {
            $user = $licenses->first()->user;

            if (! $user) {
                $this->warn("Skipping license(s) for user ID {$userId} - user not found");
                $skipped++;

                continue;
            }

            // Check if user has any active subscription (meaning they've already renewed)
            $hasActiveSubscription = $user->subscriptions()
                ->where('stripe_status', 'active')
                ->exists();

            if ($hasActiveSubscription) {
                $this->line("Skipping {$user->email} - already has active subscription");
                $skipped++;

                continue;
            }

            // Use the first (or oldest) legacy license for the email
            $license = $licenses->sortBy('created_at')->first();

            if ($dryRun) {
                $this->line("Would send to: {$user->email} (License: {$license->key})");
            } else {
                $user->notify(new LegacyLicenseThankYou($license));
                $this->line("Sent to: {$user->email}");
            }

            $sent++;
        }

        $this->newLine();
        $this->info($dryRun ? "Would send: {$sent} email(s)" : "Sent: {$sent} email(s)");
        $this->info("Skipped: {$skipped} user(s)");

        return Command::SUCCESS;
    }
}
