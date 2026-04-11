<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WallOfLoveSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WallOfLoveSubmission>
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
            'user_id' => User::factory(),
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
            'approved_by' => User::factory(),
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

    /**
     * Indicate that the submission is promoted to the homepage.
     */
    public function promoted(): static
    {
        return $this->state(fn (array $attributes) => [
            'promoted' => true,
        ]);
    }
}
