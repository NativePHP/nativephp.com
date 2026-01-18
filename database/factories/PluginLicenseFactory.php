<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PluginLicense>
 */
class PluginLicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'plugin_id' => \App\Models\Plugin::factory(),
            'stripe_payment_intent_id' => 'pi_'.$this->faker->uuid(),
            'price_paid' => $this->faker->numberBetween(1000, 10000),
            'currency' => 'USD',
            'is_grandfathered' => false,
            'purchased_at' => now(),
            'expires_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function expiresIn(int $days): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addDays($days),
        ]);
    }

    public function grandfathered(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_grandfathered' => true,
        ]);
    }
}
