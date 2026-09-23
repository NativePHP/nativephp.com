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
            ->assertSee('Masterclass &mdash; Early Bird', escape: false)
            ->assertDontSee('New Course')
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
            ->assertSee('Masterclass')
            ->assertDontSee('New Course')
            ->assertDontSee('Early Bird')
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
    public function course_dashboard_lists_the_curriculum_for_non_owners(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
        ]);
        $lesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Installing NativePHP',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Course curriculum')
            ->assertSee('Free lessons included')
            ->assertSee('Getting Started')
            ->assertSee('Installing NativePHP')
            ->assertSee(route('customer.course.lesson', $lesson), escape: false);
    }

    #[Test]
    public function course_dashboard_lists_paid_only_modules_for_non_owners(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create([
            'course_id' => $course->id,
            'title' => 'Paid Only Module',
        ]);
        $lesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
            'title' => 'Paid Only Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Course curriculum')
            ->assertSee('Paid Only Module')
            ->assertSee('Paid Only Lesson')
            ->assertSee('Every lesson unlocks when you get the course.')
            ->assertDontSee('Free lessons included')
            ->assertDontSee(route('customer.course.lesson', $lesson), escape: false);
    }

    #[Test]
    public function locked_lessons_show_a_lock_with_an_unlock_prompt(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
            'title' => 'Locked Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Locked Lesson')
            ->assertSee('Buy the course to unlock all lessons')
            ->assertSeeHtml('data-flux-tooltip');
    }

    #[Test]
    public function free_lessons_are_not_labelled_with_a_free_pill(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Watchable Lesson',
        ]);

        $html = Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Watchable Lesson')
            ->html();

        $this->assertDoesNotMatchRegularExpression('/<[^>]*badge[^>]*>\s*Free\s*</i', $html);
    }

    #[Test]
    public function course_dashboard_lists_unpublished_lessons_as_coming_soon(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Partly Released Module',
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Released Lesson',
        ]);
        CourseLesson::factory()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Unreleased Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Partly Released Module')
            ->assertSee('Released Lesson')
            ->assertSeeInOrder(['Unreleased Lesson', 'Coming Soon']);
    }

    /**
     * A published module with nothing released yet still belongs in the
     * outline — free users should see the shape of the whole course.
     */
    #[Test]
    public function course_dashboard_lists_modules_with_no_published_lessons_as_coming_soon(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Upcoming Module',
        ]);
        CourseLesson::factory()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Unreleased Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Course curriculum')
            ->assertSee('Upcoming Module')
            ->assertSee('Coming Soon')
            ->assertSee('Unreleased Lesson');
    }

    #[Test]
    public function modules_with_a_released_lesson_are_not_marked_coming_soon(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Released Module',
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Released Lesson',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Released Module')
            ->assertSee('Released Lesson')
            ->assertDontSee('Coming Soon');
    }

    /**
     * An unreleased lesson isn't gated by purchase, so it gets a Coming Soon
     * badge rather than the "buy to unlock" lock.
     */
    #[Test]
    public function unreleased_lessons_are_not_shown_as_locked(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Partly Released Module',
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Released Lesson',
        ]);
        $unreleased = CourseLesson::factory()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Unreleased Lesson',
        ]);

        $html = Livewire::actingAs($user)->test(Index::class)->html();

        $row = $this->outlineRowFor($html, $unreleased->id);

        $this->assertStringContainsString('Coming Soon', $row);
        $this->assertStringNotContainsString('unlock all lessons', $row);
    }

    /**
     * The markup for a single lesson row in the pre-purchase outline.
     */
    private function outlineRowFor(string $html, int $lessonId): string
    {
        $start = strpos($html, 'wire:key="outline-lesson-'.$lessonId.'"');

        $this->assertNotFalse($start, "Lesson {$lessonId} is missing from the outline.");

        // Up to the next row (or the end of the module's lesson list).
        $next = strpos($html, 'wire:key="outline-', $start + 1);

        return $next === false ? substr($html, $start) : substr($html, $start, $next - $start);
    }

    #[Test]
    public function a_module_with_no_lessons_at_all_shows_as_coming_soon(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Planned Module',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Planned Module')
            ->assertSee('Coming Soon');
    }

    #[Test]
    public function course_dashboard_excludes_unpublished_modules_from_the_curriculum(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->free()->create([
            'course_id' => $course->id,
            'title' => 'Draft Module',
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Lesson In Draft Module',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertDontSee('Course curriculum')
            ->assertDontSee('Draft Module')
            ->assertDontSee('Lesson In Draft Module');
    }

    #[Test]
    public function curriculum_modules_keep_their_real_position_in_the_course(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();

        foreach ([1, 2] as $sortOrder) {
            $paidModule = CourseModule::factory()->published()->create([
                'course_id' => $course->id,
                'sort_order' => $sortOrder,
            ]);
            CourseLesson::factory()->published()->create([
                'course_module_id' => $paidModule->id,
                'is_free' => false,
            ]);
        }

        $freeModule = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Third Module Is Free',
            'sort_order' => 3,
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $freeModule->id,
        ]);

        $outlineModules = Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Third Module Is Free')
            ->instance()
            ->outlineModules();

        $this->assertSame([0, 1, 2], array_keys($outlineModules->all()));
        $this->assertSame($freeModule->id, $outlineModules->last()->id);
    }

    #[Test]
    public function course_dashboard_locks_paid_lessons_inside_a_free_module_for_non_owners(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Mixed Module',
        ]);
        $freeLesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Free Intro Lesson',
            'sort_order' => 1,
        ]);
        $paidLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
            'title' => 'Paid Deep Dive Lesson',
            'sort_order' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('Free Intro Lesson')
            ->assertSee('Paid Deep Dive Lesson')
            ->assertSee(route('customer.course.lesson', $freeLesson), escape: false)
            ->assertDontSee(route('customer.course.lesson', $paidLesson), escape: false);
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
    public function paid_lesson_redirects_to_the_course_page_without_purchase(): void
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
            ->assertRedirect(route('customer.course.index'));

        $this->assertSame(
            'That lesson is part of the full course. Purchase the Masterclass to unlock it.',
            session('message'),
        );
    }

    #[Test]
    public function paid_lesson_redirect_explains_itself_on_the_course_page(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
        ]);

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('customer.course.lesson', $lesson))
            ->assertRedirect(route('customer.course.index'));

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('customer.course.index'))
            ->assertOk()
            ->assertSee('That lesson is part of the full course. Purchase the Masterclass to unlock it.', escape: false)
            ->assertSee('Build native apps');
    }

    #[Test]
    public function locked_lessons_in_the_lesson_sidebar_are_clickable(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        $freeLesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Watchable Lesson',
            'sort_order' => 1,
        ]);
        $lockedLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
            'title' => 'Locked Sidebar Lesson',
            'sort_order' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $freeLesson])
            ->assertSee('Locked Sidebar Lesson')
            ->assertSee(route('customer.course.lesson', $lockedLesson), escape: false);
    }

    #[Test]
    public function draft_lessons_in_the_lesson_sidebar_stay_unclickable(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        $freeLesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Watchable Lesson',
            'sort_order' => 1,
        ]);
        $draftLesson = CourseLesson::factory()->free()->create([
            'course_module_id' => $module->id,
            'title' => 'Draft Sidebar Lesson',
            'sort_order' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $freeLesson])
            ->assertSee('Draft Sidebar Lesson')
            ->assertSee('Coming Soon')
            ->assertDontSee(route('customer.course.lesson', $draftLesson), escape: false);
    }

    #[Test]
    public function purchase_page_orders_hero_features_notice_then_curriculum(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
        ]);
        CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
        ]);
        $lockedLesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
        ]);

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('customer.course.lesson', $lockedLesson))
            ->assertRedirect(route('customer.course.index'));

        $html = $this->withoutVite()
            ->actingAs($user)
            ->get(route('customer.course.index'))
            ->assertOk()
            ->getContent();

        $hero = strpos($html, 'Build native apps');
        $features = strpos($html, 'Zero to Published');
        $notice = strpos($html, 'Purchase the Masterclass to unlock it.');
        $curriculum = strpos($html, 'Course curriculum');

        $this->assertNotFalse($hero);
        $this->assertNotFalse($features);
        $this->assertNotFalse($notice);
        $this->assertNotFalse($curriculum);

        $this->assertLessThan($features, $hero, 'Hero should come before the feature cards');
        $this->assertLessThan($notice, $features, 'Feature cards should come before the locked-lesson notice');
        $this->assertLessThan($curriculum, $notice, 'Locked-lesson notice should come before the curriculum');
    }

    #[Test]
    public function paid_lesson_no_longer_returns_a_dead_end_403(): void
    {
        config(['app.debug' => false]);

        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
        ]);

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('customer.course.lesson', $lesson))
            ->assertStatus(302)
            ->assertRedirect(route('customer.course.index'))
            ->assertDontSee('You need Pro access to view this lesson.');
    }

    #[Test]
    public function unpublished_lesson_still_returns_404_without_purchase(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->create([
            'course_module_id' => $module->id,
            'is_free' => false,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertNotFound();
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
            ->assertSee('Coming Soon')
            ->assertSee('Draft');
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
            ->assertSee('Coming Soon')
            ->assertSee('Draft');
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
            ->assertSee('skip: false', false);
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
            ->assertSee('skip: true', false);
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
            ->assertSee('skip: true', false);
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

    #[Test]
    public function lessons_in_an_unpublished_module_are_not_accessible_to_non_admins(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertNotFound();
    }

    #[Test]
    public function admins_can_access_lessons_in_unpublished_modules(): void
    {
        config(['filament.users' => ['admin@test.com']]);
        $admin = User::factory()->create(['email' => 'admin@test.com']);

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->create([
            'course_module_id' => $module->id,
            'title' => 'Locked Away Lesson',
        ]);

        Livewire::actingAs($admin)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertOk()
            ->assertSee('Locked Away Lesson');
    }

    #[Test]
    public function lesson_notes_are_rendered_as_markdown(): void
    {
        $user = User::factory()->create();

        $course = Course::factory()->published()->create();
        $module = CourseModule::factory()->published()->free()->create(['course_id' => $course->id]);
        $lesson = CourseLesson::factory()->published()->free()->create([
            'course_module_id' => $module->id,
            'notes' => 'Run `php artisan migrate` then read [the docs](https://nativephp.com).',
        ]);

        Livewire::actingAs($user)
            ->test(LessonShow::class, ['lesson' => $lesson])
            ->assertSee('<code>php artisan migrate</code>', false)
            ->assertSee('href="https://nativephp.com"', false);
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
