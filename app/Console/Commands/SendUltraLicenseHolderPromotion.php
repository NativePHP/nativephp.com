<?php

namespace App\Console\Commands;

use App\Enums\Subscription;
use App\Models\License;
use App\Notifications\UltraLicenseHolderPromotion;
use Illuminate\Console\Command;

class SendUltraLicenseHolderPromotion extends Command
{
    protected $signature = 'ultra:send-license-holder-promo
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send a promotional email to license holders without an active subscription encouraging them to subscribe to Ultra';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No emails will be sent');
        }

        $legacyLicenses = License::query()
            ->whereNull('subscription_item_id')
            ->whereHas('user')
            ->with('user')
            ->get();

        // Group by user to avoid sending multiple emails to the same person
        $userLicenses = $legacyLicenses->groupBy('user_id');

        $eligible = 0;
        $skipped = 0;

        foreach ($userLicenses as $userId => $licenses) {
            $user = $licenses->first()->user;

            if (! $user) {
                $skipped++;

                continue;
            }

            // Skip users who already have an active subscription
            $hasActiveSubscription = $user->subscriptions()
                ->where('stripe_status', 'active')
                ->exists();

            if ($hasActiveSubscription) {
                $this->line("Skipping {$user->email} - already has active subscription");
                $skipped++;

                continue;
            }

            $license = $licenses->sortBy('created_at')->first();
            $planName = Subscription::from($license->policy_name)->name();

            if ($dryRun) {
                $this->line("Would send to: {$user->email} ({$planName})");
            } else {
                $user->notify(new UltraLicenseHolderPromotion($planName));
                $this->line("Sent to: {$user->email} ({$planName})");
            }

            $eligible++;
        }

        $this->newLine();
        $this->info("Found {$eligible} eligible license holder(s)");
        $this->info($dryRun ? "Would send: {$eligible} email(s)" : "Sent: {$eligible} email(s)");
        $this->info("Skipped: {$skipped} user(s)");

        return Command::SUCCESS;
    }
}
