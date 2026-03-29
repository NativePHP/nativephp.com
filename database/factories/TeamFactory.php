<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
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
            'name' => fake()->company(),
            'is_suspended' => false,
        ];
    }

    /**
     * Indicate that the team is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
        ]);
    }
}
