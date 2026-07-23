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
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Coupon;
use Stripe\StripeClient;
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
        Product::where('slug', 'nativephp-masterclass')->first()
            ->prices()->update(['amount' => 29900]);

        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
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
        Carbon::setTestNow('2026-06-14 23:59:59');

        Product::where('slug', 'nativephp-masterclass')->first()
            ->prices()->update(['amount' => 19900]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Build native apps')
            ->assertSee('Get Early Bird Access')
            ->assertSee('$199');

        Carbon::setTestNow();
    }

    #[Test]
    public function course_dashboard_shows_299_pricing_after_deadline(): void
    {
        Carbon::setTestNow('2026-06-15 00:00:01');

        Product::where('slug', 'nativephp-masterclass')->first()
            ->prices()->update(['amount' => 29900]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('$299')
            ->assertSee('Get Access')
            ->assertDontSee('Get Early Bird Access');

        Carbon::setTestNow();
    }

    #[Test]
    public function course_dashboard_shows_subscriber_price_with_discount(): void
    {
        $mockCoupons = new class
        {
            public function retrieve(): Coupon
            {
                return Coupon::constructFrom([
                    'id' => 'coupon_test123',
                    'valid' => true,
                    'amount_off' => 10000,
                    'percent_off' => null,
                ]);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->coupons = $mockCoupons;
        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $masterclass = Product::where('slug', 'nativephp-masterclass')->first();
        $masterclass->prices()->update(['amount' => 29900]);
        ProductPrice::factory()
            ->for($masterclass)
            ->subscriber()
            ->amount(29900)
            ->withCoupon('coupon_test123')
            ->create();

        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => 'price_test_pro']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('$199')
            ->assertSee('$299')
            ->assertSee('Your discount is applied automatically at checkout.');
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
            ->assertSee('The NativePHP Masterclass')
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
    public function admin_sees_unpublished_course_content_in_dashboard(): void
    {
        config(['filament.users' => ['admin@test.com']]);
        $admin = User::factory()->create(['email' => 'admin@test.com']);

        $course = Course::factory()->create();
        $module = CourseModule::factory()->create([
            'course_id' => $course->id,
            'title' => 'Hidden Module',
        ]);
        CourseLesson::factory()->create([
            'course_module_id' => $module->id,
            'title' => 'Hidden Lesson',
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->assertSee('Hidden Module')
            ->assertSee('Hidden Lesson')
            ->assertSee('Coming Soon');
    }

    #[Test]
    public function non_admin_owner_sees_draft_lessons_as_coming_soon_but_not_unpublished_modules(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $liveModule = CourseModule::factory()->published()->create([
            'course_id' => $course->id,
            'title' => 'Live Module',
        ]);
        $draftLesson = CourseLesson::factory()->create([
            'course_module_id' => $liveModule->id,
            'title' => 'Draft Lesson',
        ]);
        CourseModule::factory()->create([
            'course_id' => $course->id,
            'title' => 'Hidden Module',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Live Module')
            ->assertSee('Draft Lesson')
            ->assertSee('Coming Soon')
            ->assertDontSee(route('customer.course.lesson', $draftLesson), false)
            ->assertDontSee('Hidden Module');
    }

    #[Test]
    public function admin_can_view_unpublished_pro_lesson_without_purchase(): void
    {
        config(['filament.users' => ['admin@test.com']]);
        $admin = User::factory()->create(['email' => 'admin@test.com']);

        $course = Course::factory()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
            'title' => 'Hidden Pro Lesson',
        ]);

        Livewire::actingAs($admin)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSee('Hidden Pro Lesson')
            ->assertSee('Coming Soon');
    }

    #[Test]
    public function unpublished_lesson_is_not_accessible_to_non_admins(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->create([
            'course_module_id' => $module->id,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertNotFound();
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

    #[Test]
    public function first_video_in_a_session_plays_the_intro_in_full(): void
    {
        $lesson = $this->publishedVideoLesson('111111');

        Livewire::actingAs(User::factory()->create())
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSet('skipIntroOutro', false)
            ->assertDontSee('#t=9s', false);
    }

    #[Test]
    public function playing_a_video_records_it_in_the_session(): void
    {
        $lesson = $this->publishedVideoLesson('222222');

        Livewire::actingAs(User::factory()->create())
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->call('markVideoPlayed');

        $this->assertTrue(session()->has('course_video_played'));
    }

    #[Test]
    public function subsequent_videos_skip_the_intro_and_outro(): void
    {
        session()->put('course_video_played', true);

        $lesson = $this->publishedVideoLesson('333333');

        Livewire::actingAs(User::factory()->create())
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSet('skipIntroOutro', true)
            ->assertSee('#t=9s', false);
    }

    #[Test]
    public function playing_the_first_video_makes_later_videos_in_the_session_skip(): void
    {
        $user = User::factory()->create();
        $firstLesson = $this->publishedVideoLesson('444444');
        $secondLesson = $this->publishedVideoLesson('555555');

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $firstLesson])
            ->assertSet('skipIntroOutro', false)
            ->call('markVideoPlayed');

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $secondLesson])
            ->assertSet('skipIntroOutro', true)
            ->assertSee('#t=9s', false);
    }

    #[Test]
    public function lesson_page_shows_the_full_course_outline(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $currentModule = CourseModule::factory()->published()->create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'sort_order' => 1,
        ]);
        $otherModule = CourseModule::factory()->published()->create([
            'course_id' => $course->id,
            'title' => 'Going Deeper',
            'sort_order' => 2,
        ]);
        $currentLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $currentModule->id,
            'title' => 'Intro Lesson',
        ]);
        CourseLesson::factory()->published()->create([
            'course_module_id' => $otherModule->id,
            'title' => 'Advanced Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $currentLesson])
            ->assertSee('Course outline')
            ->assertSee('Getting Started')
            ->assertSee('Going Deeper')
            ->assertSee('Advanced Lesson');
    }

    #[Test]
    public function coming_soon_banner_shows_when_no_lessons_have_videos(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'vimeo_id' => null,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('recording the lessons now');
    }

    #[Test]
    public function coming_soon_banner_is_hidden_when_a_lesson_has_a_video(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'vimeo_id' => '123456',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertDontSee('recording the lessons now');
    }

    #[Test]
    public function course_module_list_does_not_show_free_or_pro_pills(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Starter Module',
            'description' => null,
        ]);
        CourseModule::factory()->published()->create([
            'course_id' => $course->id,
            'title' => 'Advanced Module',
            'description' => null,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Starter Module')
            ->assertSee('Advanced Module')
            ->assertDontSee('Free')
            ->assertDontSee('Pro');
    }

    #[Test]
    public function draft_lessons_show_in_the_outline_as_coming_soon_but_are_not_clickable_for_non_admins(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $currentLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'title' => 'Current Lesson',
        ]);
        $draftLesson = CourseLesson::factory()->create([
            'course_module_id' => $module->id,
            'title' => 'Draft Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $currentLesson])
            ->assertSee('Draft Lesson')
            ->assertSee('Coming Soon')
            ->assertDontSee(route('customer.course.lesson', $draftLesson), false);
    }

    #[Test]
    public function draft_lessons_are_clickable_in_the_outline_for_admins(): void
    {
        config(['filament.users' => ['admin@test.com']]);
        $admin = User::factory()->create(['email' => 'admin@test.com']);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $currentLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'title' => 'Current Lesson',
        ]);
        $draftLesson = CourseLesson::factory()->create([
            'course_module_id' => $module->id,
            'title' => 'Draft Lesson',
        ]);

        Livewire::actingAs($admin)
            ->test(LessonShow::class, ['lesson' => $currentLesson])
            ->assertSee('Draft Lesson')
            ->assertSee('Coming Soon')
            ->assertSee(route('customer.course.lesson', $draftLesson), false);
    }

    #[Test]
    public function draft_video_lessons_do_not_hide_the_coming_soon_banner_for_non_admins(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        CourseLesson::factory()->create([
            'course_module_id' => $module->id,
            'vimeo_id' => '123456',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('recording the lessons now');
    }

    #[Test]
    public function completed_lessons_are_struck_through_in_the_outline(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $currentLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'title' => 'Current Lesson',
        ]);
        $doneLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'title' => 'Done Lesson',
        ]);
        LessonProgress::create([
            'user_id' => $user->id,
            'course_lesson_id' => $doneLesson->id,
            'completed_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $currentLesson])
            ->assertSee('Done Lesson')
            ->assertSeeHtml('line-through');
    }

    #[Test]
    public function outline_lesson_titles_have_a_hover_title_attribute_and_are_not_struck_through_when_incomplete(): void
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
            'title' => 'Hover For Full Title',
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSeeHtml('title="Hover For Full Title"')
            ->assertDontSeeHtml('line-through');
    }

    private function publishedVideoLesson(string $vimeoId): CourseLesson
    {
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);

        return CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'vimeo_id' => $vimeoId,
        ]);
    }
}
