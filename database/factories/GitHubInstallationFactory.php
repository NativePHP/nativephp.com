<?php

namespace Database\Factories;

use App\Models\GitHubInstallation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GitHubInstallation>
 */
class GitHubInstallationFactory extends Factory
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
            'installation_id' => fake()->unique()->randomNumber(8),
            'account_login' => fake()->userName(),
            'account_type' => 'User',
            'account_id' => fake()->randomNumber(8),
            'selection_type' => 'all',
            'repository_selection' => null,
        ];
    }

    public function forOrganization(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => 'Organization',
        ]);
    }

    public function selectedRepos(array $repos): static
    {
        return $this->state(fn (array $attributes) => [
            'selection_type' => 'selected',
            'repository_selection' => $repos,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'suspended_at' => now(),
        ]);
    }
}
