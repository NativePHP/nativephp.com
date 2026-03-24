<?php

namespace Database\Factories;

use App\Enums\StripeConnectStatus;
use App\Models\DeveloperAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeveloperAccount>
 */
class DeveloperAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_connect_account_id' => 'acct_'.fake()->unique()->bothify('??############'),
            'stripe_connect_status' => StripeConnectStatus::Active,
            'payouts_enabled' => true,
            'charges_enabled' => true,
            'onboarding_completed_at' => now(),
            'country' => 'US',
            'payout_currency' => 'USD',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'stripe_connect_status' => StripeConnectStatus::Pending,
            'payouts_enabled' => false,
            'charges_enabled' => false,
            'onboarding_completed_at' => null,
        ]);
    }

    public function withAcceptedTerms(?string $version = null): static
    {
        return $this->state(fn (array $attributes) => [
            'accepted_plugin_terms_at' => now(),
            'plugin_terms_version' => $version ?? DeveloperAccount::CURRENT_PLUGIN_TERMS_VERSION,
        ]);
    }
}
