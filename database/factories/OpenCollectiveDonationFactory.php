<?php

namespace Database\Factories;

use App\Models\OpenCollectiveDonation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OpenCollectiveDonation>
 */
class OpenCollectiveDonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'webhook_id' => fake()->unique()->randomNumber(6),
            'order_id' => fake()->unique()->randomNumber(5),
            'order_idv2' => fake()->uuid(),
            'amount' => fake()->numberBetween(1000, 10000),
            'currency' => 'USD',
            'interval' => null,
            'from_collective_id' => fake()->randomNumber(5),
            'from_collective_name' => fake()->name(),
            'from_collective_slug' => fake()->slug(2),
            'raw_payload' => [],
        ];
    }

    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'claimed_at' => now(),
        ]);
    }
}
