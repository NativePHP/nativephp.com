<?php

namespace Database\Factories;

use App\Enums\DocsCoverageCategory;
use App\Enums\DocsPlatform;
use App\Models\DocsCoverageGap;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocsCoverageGap>
 */
class DocsCoverageGapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => DocsPlatform::Mobile,
            'category' => DocsCoverageCategory::ConfigKey,
            'identifier' => $this->faker->unique()->word(),
            'documented' => $this->faker->boolean(),
            'source_path' => 'config/nativephp.php',
            'checked_at' => now(),
        ];
    }
}
