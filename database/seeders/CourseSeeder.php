<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::create([
            'title' => 'NativePHP: Build Native Mobile Apps with PHP & Laravel',
            'slug' => 'nativephp-build-native-mobile-apps-with-php-laravel',
            'description' => 'Learn how to build native mobile applications using PHP and Laravel with NativePHP.',
            'is_published' => true,
            'sort_order' => 0,
        ]);

        $this->seedGettingStarted($course);
        $this->seedYourFirstMobileApp($course);
        $this->seedBuildingRealWorldApp($course);
        $this->seedDeployment($course);
    }

    private function seedGettingStarted(Course $course): void
    {
        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'is_free' => true,
            'is_published' => true,
            'sort_order' => 0,
        ]);

        $lessons = [
            'What is NativePHP',
            'Prerequisites and What to Expect',
            'Web vs Mobile vs Desktop',
        ];

        foreach ($lessons as $index => $title) {
            CourseLesson::create([
                'course_module_id' => $module->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'is_free' => true,
                'is_published' => true,
                'sort_order' => $index,
            ]);
        }
    }

    private function seedYourFirstMobileApp(Course $course): void
    {
        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Your First Mobile App',
            'slug' => 'your-first-mobile-app',
            'is_free' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $lessons = [
            'Install NativePHP',
            'The Jump App',
            'Environment Setup for iOS',
            'Environment Setup for Android',
            'The Config File',
            'Development & Hot Reload',
        ];

        foreach ($lessons as $index => $title) {
            CourseLesson::create([
                'course_module_id' => $module->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'is_free' => true,
                'is_published' => true,
                'sort_order' => $index,
            ]);
        }
    }

    private function seedBuildingRealWorldApp(Course $course): void
    {
        CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Building a Real-World App',
            'slug' => 'building-a-real-world-app',
            'is_free' => false,
            'is_published' => true,
            'sort_order' => 2,
        ]);
    }

    private function seedDeployment(Course $course): void
    {
        CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Deployment',
            'slug' => 'deployment',
            'is_free' => false,
            'is_published' => true,
            'sort_order' => 3,
        ]);
    }
}
