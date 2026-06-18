<?php

namespace App\Console\Commands;

use App\Enums\Subscription;
use App\Models\User;
use App\Notifications\UltraUpgradePromotion;
use Illuminate\Console\Command;

class SendUltraUpgradePromotion extends Command
{
    protected $signature = 'ultra:send-upgrade-promo
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send a promotional email to Mini and Pro subscribers encouraging them to upgrade to Ultra';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No emails will be sent');
        }

        $miniPriceIds = array_filter([
            config('subscriptions.plans.mini.stripe_price_id'),
            config('subscriptions.plans.mini.stripe_price_id_eap'),
        ]);

        $proPriceIds = array_filter([
            config('subscriptions.plans.pro.stripe_price_id'),
            config('subscriptions.plans.pro.stripe_price_id_eap'),
            config('subscriptions.plans.pro.stripe_price_id_discounted'),
        ]);

        $eligiblePriceIds = array_merge($miniPriceIds, $proPriceIds);

        $users = User::query()
            ->whereNotNull('email_verified_at')
            ->whereHas('subscriptions', function ($query) use ($eligiblePriceIds) {
                $query->where('stripe_status', 'active')
                    ->where('is_comped', false)
                    ->whereIn('stripe_price', $eligiblePriceIds);
            })
            ->get();

        $this->info("Found {$users->count()} eligible subscriber(s)");

        $sent = 0;

        foreach ($users as $user) {
            $priceId = $user->subscriptions()
                ->where('stripe_status', 'active')
                ->where('is_comped', false)
                ->whereIn('stripe_price', $eligiblePriceIds)
                ->value('stripe_price');

            $planName = Subscription::fromStripePriceId($priceId)->name();

            if ($dryRun) {
                $this->line("Would send to: {$user->email} ({$planName})");
            } else {
                $user->notify(new UltraUpgradePromotion($planName));
                $this->line("Sent to: {$user->email} ({$planName})");
            }

            $sent++;
        }

        $this->newLine();
        $this->info($dryRun ? "Would send: {$sent} email(s)" : "Sent: {$sent} email(s)");

        return Command::SUCCESS;
    }
}
