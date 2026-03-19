<?php

namespace Tests\Feature;

use App\Models\User;
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

    #[Test]
    public function course_checkout_redirects_to_stripe_with_cart_success_url(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $stripeSessionUrl = 'https://checkout.stripe.com/test-session';
        $capturedParams = null;

        $mockCheckoutSessions = new class($stripeSessionUrl, $capturedParams)
        {
            public function __construct(
                private string $url,
                private &$capturedParams,
            ) {}

            public function create(array $params): object
            {
                $this->capturedParams = $params;

                return (object) [
                    'id' => 'cs_test123',
                    'url' => $this->url,
                ];
            }
        };

        $mockCheckout = new \stdClass;
        $mockCheckout->sessions = $mockCheckoutSessions;

        $mockCustomers = new class
        {
            public function retrieve(): \Stripe\Customer
            {
                return \Stripe\Customer::constructFrom([
                    'id' => 'cus_test123',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }
        };

        $mockStripeClient = $this->createMock(\Stripe\StripeClient::class);
        $mockStripeClient->checkout = $mockCheckout;
        $mockStripeClient->customers = $mockCustomers;

        $this->app->bind(\Stripe\StripeClient::class, fn () => $mockStripeClient);

        $this
            ->actingAs($user)
            ->post(route('course.checkout'))
            ->assertRedirect($stripeSessionUrl);

        $this->assertNotNull($capturedParams, 'Stripe checkout session should have been created');
        $this->assertStringContainsString(route('cart.success'), $capturedParams['success_url']);
        $this->assertStringContainsString('{CHECKOUT_SESSION_ID}', $capturedParams['success_url']);
    }
}
