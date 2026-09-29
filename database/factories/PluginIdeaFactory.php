<?php

namespace Database\Factories;

use App\Models\PluginIdea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginIdea>
 */
class PluginIdeaFactory extends Factory
{
    protected $model = PluginIdea::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
        ];
    }
}
