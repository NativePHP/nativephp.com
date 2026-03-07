<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CoursePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function course_page_loads_successfully(): void
    {
        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('The NativePHP Masterclass')
            ->assertSee('Early Bird');
    }

    #[Test]
    public function course_page_contains_mailcoach_signup_form(): void
    {
        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertSee('simonhamp.mailcoach.app/subscribe/', false)
            ->assertSee('Join Waitlist');
    }

    #[Test]
    public function course_page_contains_checkout_form(): void
    {
        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertSee(route('course.checkout'), false)
            ->assertSee('Get Early Bird Access');
    }

    #[Test]
    public function course_checkout_redirects_guests_to_login(): void
    {
        $this
            ->post(route('course.checkout'))
            ->assertRedirect(route('customer.login'));
    }
}
