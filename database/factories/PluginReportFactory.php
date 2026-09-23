<?php

namespace Database\Factories;

use App\Enums\PluginReportCategory;
use App\Enums\PluginReportStatus;
use App\Models\Plugin;
use App\Models\PluginReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginReport>
 */
class PluginReportFactory extends Factory
{
    protected $model = PluginReport::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plugin_id' => Plugin::factory(),
            'user_id' => User::factory(),
            'category' => $this->faker->randomElement(PluginReportCategory::cases()),
            'message' => $this->faker->paragraph(),
            'status' => PluginReportStatus::Open,
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PluginReportStatus::Resolved,
            'resolved_at' => now(),
            'resolved_by' => User::factory(),
        ]);
    }

    public function dismissed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PluginReportStatus::Dismissed,
            'resolved_at' => now(),
            'resolved_by' => User::factory(),
        ]);
    }
}
