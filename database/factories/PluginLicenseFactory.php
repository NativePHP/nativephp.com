<?php

namespace Database\Factories;

use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginLicense>
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
            'user_id' => User::factory(),
            'plugin_id' => Plugin::factory(),
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

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'refunded_at' => now(),
            'stripe_refund_id' => 're_'.$this->faker->uuid(),
            'refunded_by' => User::factory(),
        ]);
    }
}
