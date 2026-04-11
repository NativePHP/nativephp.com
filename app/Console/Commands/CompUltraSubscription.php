<?php

namespace App\Console\Commands;

use App\Enums\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class CompUltraSubscription extends Command
{
    protected $signature = 'ultra:comp {email : The email address of the user to comp}';

    protected $description = 'Create a comped Ultra subscription for a user using the dedicated $0 Stripe price';

    public function handle(): int
    {
        $compedPriceId = config('subscriptions.plans.max.stripe_price_id_comped');

        if (! $compedPriceId) {
            $this->error('STRIPE_ULTRA_COMP_PRICE_ID is not configured.');

            return self::FAILURE;
        }

        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User not found: {$email}");

            return self::FAILURE;
        }

        $existingSubscription = $user->subscription('default');

        if ($existingSubscription && $existingSubscription->active()) {
            $currentPlan = 'unknown';

            try {
                $currentPlan = Subscription::fromStripePriceId(
                    $existingSubscription->items->first()?->stripe_price ?? $existingSubscription->stripe_price
                )->name();
            } catch (\Exception) {
            }

            $this->error("User already has an active {$currentPlan} subscription. Cancel it first or use swap.");

            return self::FAILURE;
        }

        $user->createOrGetStripeCustomer();

        $user->newSubscription('default', $compedPriceId)->create();

        $this->info("Comped Ultra subscription created for {$email}.");

        return self::SUCCESS;
    }
}
