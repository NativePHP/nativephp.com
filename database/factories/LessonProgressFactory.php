<?php

namespace Database\Factories;

use App\Models\CourseLesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonProgress>
 */
class LessonProgressFactory extends Factory
{
    protected $model = LessonProgress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_lesson_id' => CourseLesson::factory(),
            'completed_at' => now(),
        ];
    }

    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => null,
        ]);
    }
}
