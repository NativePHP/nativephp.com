<?php

namespace Tests\Feature;

use App\Livewire\Customer\Course\Index;
use App\Livewire\Customer\Course\LessonShow;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\LessonProgress;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CourseContentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function course_page_loads_successfully(): void
    {
        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('The NativePHP Masterclass');
    }

    #[Test]
    public function course_page_shows_pricing(): void
    {
        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('$101')
            ->assertSee('$299');
    }

    #[Test]
    public function course_dashboard_requires_authentication(): void
    {
        $this
            ->get(route('customer.course.index'))
            ->assertRedirect();
    }

    #[Test]
    public function course_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this
            ->withoutVite()
            ->actingAs($user)
            ->get(route('customer.course.index'))
            ->assertStatus(200);
    }

    #[Test]
    public function course_dashboard_shows_purchase_page_for_non_owners(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Build native apps')
            ->assertSee('Get Early Bird Access');
    }

    #[Test]
    public function course_dashboard_shows_modules_and_lessons_for_owners(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Test Module',
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Test Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Test Module')
            ->assertSee('Test Lesson');
    }

    #[Test]
    public function free_lesson_is_accessible_without_purchase(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Free Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSee('Free Lesson');
    }

    #[Test]
    public function pro_lesson_is_blocked_without_purchase(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertForbidden();
    }

    #[Test]
    public function pro_lesson_is_accessible_with_purchase(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
            'title' => 'Pro Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSee('Pro Lesson');
    }

    #[Test]
    public function user_can_toggle_lesson_completion(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSee('Mark Complete')
            ->call('toggleComplete')
            ->assertSee('Completed');

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'course_lesson_id' => $lesson->id,
        ]);
    }

    #[Test]
    public function user_can_uncomplete_a_lesson(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
        ]);

        LessonProgress::create([
            'user_id' => $user->id,
            'course_lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSee('Completed')
            ->call('toggleComplete')
            ->assertSee('Mark Complete');

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'course_lesson_id' => $lesson->id,
            'completed_at' => null,
        ]);
    }

    #[Test]
    public function course_model_has_modules_relationship(): void
    {
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);

        $this->assertTrue($course->modules->contains($module));
    }

    #[Test]
    public function module_has_lessons_relationship(): void
    {
        $module = CourseModule::factory()->create();
        $lesson = CourseLesson::factory()->create(['course_module_id' => $module->id]);

        $this->assertTrue($module->lessons->contains($lesson));
    }
}
