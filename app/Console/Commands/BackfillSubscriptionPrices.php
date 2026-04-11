<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Sleep;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;

class BackfillSubscriptionPrices extends Command
{
    protected $signature = 'subscriptions:backfill-prices';

    protected $description = 'Backfill price_paid on subscriptions from Stripe invoices';

    public function handle(): int
    {
        $subscriptions = Subscription::whereNull('price_paid')->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions need backfilling.');

            return self::SUCCESS;
        }

        $this->info("Backfilling {$subscriptions->count()} subscriptions...");

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        $errors = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $invoices = Cashier::stripe()->invoices->all([
                    'subscription' => $subscription->stripe_id,
                    'limit' => 1,
                ]);

                if (! empty($invoices->data)) {
                    $subscription->update([
                        'price_paid' => max(0, $invoices->data[0]->total),
                    ]);
                }
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Failed for subscription {$subscription->stripe_id}: {$e->getMessage()}");
            }

            $bar->advance();
            Sleep::usleep(100_000);
        }

        $bar->finish();
        $this->newLine(2);

        if ($errors > 0) {
            $this->warn("{$errors} subscription(s) failed to backfill.");
        }

        $this->info('Backfill complete.');

        return self::SUCCESS;
    }
}
