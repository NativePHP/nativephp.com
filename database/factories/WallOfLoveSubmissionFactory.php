<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WallOfLoveSubmission>
 */
class WallOfLoveSubmissionFactory extends Factory
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
            'name' => fake()->name(),
            'company' => fake()->optional(0.7)->company(),
            'photo_path' => null, // Photos are optional and would need real files
            'url' => fake()->optional(0.6)->url(),
            'testimonial' => fake()->optional(0.8)->paragraph(3),
            'approved_at' => null,
            'approved_by' => null,
        ];
    }

    /**
     * Indicate that the submission is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'approved_by' => \App\Models\User::factory(),
        ]);
    }

    /**
     * Indicate that the submission is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }
}
