<?php

namespace Database\Factories;

use App\Models\License;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubLicense>
 */
class SubLicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_license_id' => License::factory(),
            'anystack_id' => fake()->uuid(),
            'name' => fake()->optional()->words(2, true),
            'key' => fake()->uuid(), // In tests, we'll use fake keys since we're not hitting Anystack
            'is_suspended' => false,
            'expires_at' => null,
        ];
    }

    /**
     * Indicate that the sub-license is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
        ]);
    }

    /**
     * Indicate that the sub-license has an expiry date.
     */
    public function withExpiry(?\DateTime $expiresAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $expiresAt ?: fake()->dateTimeBetween('now', '+1 year'),
        ]);
    }

    /**
     * Indicate that the sub-license is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'is_suspended' => false,
        ]);
    }
}
