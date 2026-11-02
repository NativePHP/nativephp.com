<?php

namespace Database\Factories;

use App\Models\Plugin;
use App\Models\PluginRating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginRating>
 */
class PluginRatingFactory extends Factory
{
    protected $model = PluginRating::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plugin_id' => Plugin::factory(),
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
        ];
    }
}
