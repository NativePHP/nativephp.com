<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\MaxToUltraAnnouncement;
use Illuminate\Console\Command;

class SendMaxToUltraAnnouncement extends Command
{
    protected $signature = 'ultra:send-announcement
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send a one-time announcement email to paying Max subscribers about the Ultra upgrade';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No emails will be sent');
        }

        $maxPriceIds = array_filter([
            config('subscriptions.plans.max.stripe_price_id'),
            config('subscriptions.plans.max.stripe_price_id_monthly'),
            config('subscriptions.plans.max.stripe_price_id_eap'),
            config('subscriptions.plans.max.stripe_price_id_discounted'),
        ]);

        $users = User::query()
            ->whereHas('subscriptions', function ($query) use ($maxPriceIds) {
                $query->where('stripe_status', 'active')
                    ->where('is_comped', false)
                    ->whereIn('stripe_price', $maxPriceIds);
            })
            ->get();

        $this->info("Found {$users->count()} paying Max subscriber(s)");

        $sent = 0;

        foreach ($users as $user) {
            if ($dryRun) {
                $this->line("Would send to: {$user->email}");
            } else {
                $user->notify(new MaxToUltraAnnouncement);
                $this->line("Sent to: {$user->email}");
            }

            $sent++;
        }

        $this->newLine();
        $this->info($dryRun ? "Would send: {$sent} email(s)" : "Sent: {$sent} email(s)");

        return Command::SUCCESS;
    }
}
