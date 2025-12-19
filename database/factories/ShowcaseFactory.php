<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

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

    public function withScreenshots(int $count = 3, bool $tall = false): static
    {
        return $this->state(function (array $attributes) use ($count, $tall) {
            $screenshots = [];

            for ($i = 0; $i < $count; $i++) {
                $screenshots[] = $this->generatePlaceholderScreenshot($tall);
            }

            return ['screenshots' => $screenshots];
        });
    }

    public function withTallScreenshots(int $count = 3): static
    {
        return $this->withScreenshots($count, tall: true);
    }

    public function withWideScreenshots(int $count = 3): static
    {
        return $this->withScreenshots($count, tall: false);
    }

    protected function generatePlaceholderScreenshot(bool $tall = false): string
    {
        $width = $tall ? 390 : 1280;
        $height = $tall ? 844 : 720;

        $image = imagecreatetruecolor($width, $height);

        $colors = [
            [99, 102, 241],   // Indigo
            [139, 92, 246],   // Purple
            [236, 72, 153],   // Pink
            [14, 165, 233],   // Sky
            [34, 197, 94],    // Green
            [249, 115, 22],   // Orange
        ];

        $color = fake()->randomElement($colors);
        $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        imagefill($image, 0, 0, $bgColor);

        $white = imagecolorallocate($image, 255, 255, 255);

        if ($tall) {
            // Draw phone UI elements
            imagefilledrectangle($image, 20, 60, $width - 20, 120, $white);
            imagefilledrectangle($image, 20, 140, $width - 20, 400, imagecolorallocatealpha($image, 255, 255, 255, 80));
            imagefilledrectangle($image, 20, 420, ($width - 20) / 2 - 10, 600, imagecolorallocatealpha($image, 255, 255, 255, 80));
            imagefilledrectangle($image, ($width - 20) / 2 + 10, 420, $width - 20, 600, imagecolorallocatealpha($image, 255, 255, 255, 80));
        } else {
            // Draw desktop UI elements
            imagefilledrectangle($image, 0, 0, $width, 40, imagecolorallocatealpha($image, 0, 0, 0, 80));
            imagefilledrectangle($image, 20, 60, 250, $height - 20, imagecolorallocatealpha($image, 255, 255, 255, 80));
            imagefilledrectangle($image, 270, 60, $width - 20, $height - 20, imagecolorallocatealpha($image, 255, 255, 255, 90));
        }

        $filename = 'showcase-screenshots/'.fake()->uuid().'.png';
        Storage::disk('public')->makeDirectory('showcase-screenshots');

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }
}
