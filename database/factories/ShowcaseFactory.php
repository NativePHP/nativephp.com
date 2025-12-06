<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Showcase>
 */
class ShowcaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasMobile = fake()->boolean(50);
        $hasDesktop = fake()->boolean(50);

        if (! $hasMobile && ! $hasDesktop) {
            $hasMobile = fake()->boolean();
            $hasDesktop = ! $hasMobile;
        }

        return [
            'user_id' => User::factory(),
            'title' => fake()->words(rand(2, 4), true),
            'description' => fake()->paragraph(3),
            'image' => null,
            'screenshots' => null,
            'has_mobile' => $hasMobile,
            'has_desktop' => $hasDesktop,
            'play_store_url' => $hasMobile ? fake()->optional(0.7)->url() : null,
            'app_store_url' => $hasMobile ? fake()->optional(0.7)->url() : null,
            'windows_download_url' => $hasDesktop ? fake()->optional(0.6)->url() : null,
            'macos_download_url' => $hasDesktop ? fake()->optional(0.6)->url() : null,
            'linux_download_url' => $hasDesktop ? fake()->optional(0.5)->url() : null,
            'certified_nativephp' => true,
            'approved_at' => null,
            'approved_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => fake()->dateTimeBetween('-60 days', 'now'),
            'approved_by' => User::factory(),
        ]);
    }

    public function recentlyApproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => fake()->dateTimeBetween('-25 days', 'now'),
            'approved_by' => User::factory(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_mobile' => true,
            'has_desktop' => false,
            'play_store_url' => fake()->optional(0.7)->url(),
            'app_store_url' => fake()->optional(0.7)->url(),
            'windows_download_url' => null,
            'macos_download_url' => null,
            'linux_download_url' => null,
        ]);
    }

    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_mobile' => false,
            'has_desktop' => true,
            'play_store_url' => null,
            'app_store_url' => null,
            'windows_download_url' => fake()->optional(0.6)->url(),
            'macos_download_url' => fake()->optional(0.6)->url(),
            'linux_download_url' => fake()->optional(0.5)->url(),
        ]);
    }

    public function both(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_mobile' => true,
            'has_desktop' => true,
            'play_store_url' => fake()->optional(0.7)->url(),
            'app_store_url' => fake()->optional(0.7)->url(),
            'windows_download_url' => fake()->optional(0.6)->url(),
            'macos_download_url' => fake()->optional(0.6)->url(),
            'linux_download_url' => fake()->optional(0.5)->url(),
        ]);
    }
}
