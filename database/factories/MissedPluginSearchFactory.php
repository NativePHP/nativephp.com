<?php

namespace Database\Factories;

use App\Models\MissedPluginSearch;
use App\Models\PluginIdea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MissedPluginSearch>
 */
class MissedPluginSearchFactory extends Factory
{
    protected $model = MissedPluginSearch::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plugin_idea_id' => PluginIdea::factory(),
            'term' => $this->faker->words(2, true),
            'use_case' => $this->faker->sentence(),
        ];
    }

    public function unclassified(): static
    {
        return $this->state(fn (array $attributes) => [
            'plugin_idea_id' => null,
        ]);
    }
}
