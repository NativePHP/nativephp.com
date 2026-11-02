<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'receives_notification_emails' => true,
            'receives_new_plugin_notifications' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withGitHubApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'github_id' => (string) fake()->randomNumber(8),
            'github_username' => fake()->userName(),
            'github_token' => encrypt('ghu_test_token_'.Str::random(20)),
            'github_auth_type' => 'app',
        ]);
    }

    public function withLegacyGitHub(): static
    {
        return $this->state(fn (array $attributes) => [
            'github_id' => (string) fake()->randomNumber(8),
            'github_username' => fake()->userName(),
            'github_token' => encrypt('gho_test_token_'.Str::random(20)),
            'github_auth_type' => 'oauth',
        ]);
    }
}
