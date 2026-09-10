<?php

namespace Database\Factories;

use App\Models\DocsFeedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocsFeedback>
 */
class DocsFeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => 'mobile',
            'version' => '4',
            'page' => 'getting-started/introduction',
            'helpful' => $this->faker->boolean(),
            'comment' => $this->faker->optional()->sentence(),
            'ip_hash' => hash('sha256', $this->faker->ipv4()),
        ];
    }
}
