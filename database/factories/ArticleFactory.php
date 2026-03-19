<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'slug' => fake()->unique()->slug(3),
            'title' => fake()->sentence(),
            'excerpt' => fake()->paragraph(1, false),
            'content' => implode(PHP_EOL.PHP_EOL, fake()->paragraphs()),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'published_at' => now(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'published_at' => now()->addMinute(),
        ]);
    }
}
