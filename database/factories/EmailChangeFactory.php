<?php

namespace Database\Factories;

use App\Models\EmailChange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailChange>
 */
class EmailChangeFactory extends Factory
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
            'old_email' => fake()->unique()->safeEmail(),
            'new_email' => fake()->unique()->safeEmail(),
            'ip_address' => fake()->ipv4(),
            'expires_at' => now()->addHour(),
            'confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the email change has been confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the email change request has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinute(),
        ]);
    }
}
