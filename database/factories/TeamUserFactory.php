<?php

namespace Database\Factories;

use App\Enums\TeamUserRole;
use App\Enums\TeamUserStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamUser>
 */
class TeamUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => null,
            'email' => fake()->unique()->safeEmail(),
            'role' => TeamUserRole::Member,
            'status' => TeamUserStatus::Pending,
            'invitation_token' => bin2hex(random_bytes(32)),
            'invited_at' => now(),
            'accepted_at' => null,
        ];
    }

    /**
     * Indicate the team user is active (accepted invitation).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'status' => TeamUserStatus::Active,
            'invitation_token' => null,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Indicate the team user has been removed.
     */
    public function removed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TeamUserStatus::Removed,
            'invitation_token' => null,
        ]);
    }
}
