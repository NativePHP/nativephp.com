<?php

namespace Database\Factories;

use App\Models\PluginPayout;
use App\Models\PluginPayoutAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginPayoutAttempt>
 */
class PluginPayoutAttemptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plugin_payout_id' => PluginPayout::factory(),
            'succeeded' => false,
            'charge_id' => 'ch_'.$this->faker->uuid(),
            'stripe_transfer_id' => null,
            'error_message' => 'Stripe error: something went wrong',
        ];
    }

    public function succeeded(): static
    {
        return $this->state(fn () => [
            'succeeded' => true,
            'stripe_transfer_id' => 'tr_'.$this->faker->uuid(),
            'error_message' => null,
        ]);
    }
}
