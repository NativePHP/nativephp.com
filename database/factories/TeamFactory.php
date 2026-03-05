<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'is_suspended' => false,
            'extra_seats' => 0,
        ];
    }

    public function withExtraSeats(int $count): static
    {
        return $this->state(fn () => [
            'extra_seats' => $count,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'is_suspended' => true,
        ]);
    }
}
