<?php

namespace Tests\Feature;

use App\Jobs\HandleInvoicePaidJob;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginPrice;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Notifications\PurchaseReceipt;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Subscription;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Checkout\Session;
use Stripe\Coupon;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\StripeClient;
use Tests\TestCase;

class CoursePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Bind a StripeClient mock that serves a $100-off coupon for display price calculations.
     */
    private function mockStripeCoupon(): void
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
    }

    /**
     * Bind a mocked StripeClient and return a holder object whose ->params
     * property captures the checkout session params sent to Stripe.
     */
    private function captureStripeCheckoutParams(): \stdClass
    {
        $captured = new \stdClass;
        $captured->params = null;

        $mockCheckoutSessions = new class($captured)
        {
            public function __construct(private \stdClass $captured) {}

            public function create(array $params): Session
            {
                $this->captured->params = $params;

                return Session::constructFrom([
                    'id' => 'cs_test123',
                    'url' => 'https://checkout.stripe.com/test-session',
                ]);
            }
        };

        $mockCheckout = new \stdClass;
        $mockCheckout->sessions = $mockCheckoutSessions;

        $mockCustomers = new class
        {
            public function retrieve(): Customer
            {
                return Customer::constructFrom([
                    'id' => 'cus_test123',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->checkout = $mockCheckout;
        $mockStripeClient->customers = $mockCustomers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        return $captured;
    }

    #[Test]
    public function course_page_loads_successfully(): void
    {
        Product::where('slug', 'nativephp-masterclass')->first()
            ->prices()->update(['amount' => 29900]);

        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('The NativePHP Masterclass')
            ->assertSee('$299');
    }

    #[Test]
    public function course_page_shows_price_from_database(): void
    {
        Product::where('slug', 'nativephp-masterclass')->first()
            ->prices()->update(['amount' => 24900]);

        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('$249');
    }

    #[Test]
    public function course_page_shows_discounted_price_to_subscribers(): void
    {
        $this->mockStripeCoupon();

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

        $this
            ->withoutVite()
            ->actingAs($user)
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('$199')
            ->assertSee('$299')
            ->assertSee('Your discount is applied automatically at checkout.');
    }

    #[Test]
    public function course_page_falls_back_to_299_pricing_without_database_prices(): void
    {
        Carbon::setTestNow('2026-06-15 00:00:01');

        Product::where('slug', 'nativephp-masterclass')->first()
            ->prices()->delete();

        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('$299');

        Carbon::setTestNow();
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
            ->assertSee('Buy Now');
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
        Carbon::setTestNow('2026-06-14 23:59:59');
        config(['services.stripe.course_price_id_199' => 'price_test123']);

        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $stripeSessionUrl = 'https://checkout.stripe.com/test-session';
        $capturedParams = null;

        $mockCheckoutSessions = new class($stripeSessionUrl, $capturedParams)
        {
            public function __construct(
                private string $url,
                private &$capturedParams,
            ) {}

            public function create(array $params): Session
            {
                $this->capturedParams = $params;

                return Session::constructFrom([
                    'id' => 'cs_test123',
                    'url' => $this->url,
                ]);
            }
        };

        $mockCheckout = new \stdClass;
        $mockCheckout->sessions = $mockCheckoutSessions;

        $mockCustomers = new class
        {
            public function retrieve(): Customer
            {
                return Customer::constructFrom([
                    'id' => 'cus_test123',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->checkout = $mockCheckout;
        $mockStripeClient->customers = $mockCustomers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $this
            ->actingAs($user)
            ->post(route('course.checkout'))
            ->assertRedirect($stripeSessionUrl);

        $this->assertNotNull($capturedParams, 'Stripe checkout session should have been created');
        $this->assertStringContainsString(route('cart.success'), $capturedParams['success_url']);
        $this->assertStringContainsString('{CHECKOUT_SESSION_ID}', $capturedParams['success_url']);

        $this->assertTrue($capturedParams['tax_id_collection']['enabled']);
        $this->assertSame('auto', $capturedParams['customer_update']['address'] ?? null, 'Tax ID collection requires customer_update[address] = auto for existing customers');
        $this->assertSame('auto', $capturedParams['customer_update']['name'] ?? null);

        Carbon::setTestNow();
    }

    #[Test]
    public function course_checkout_does_not_use_the_cart(): void
    {
        $captured = $this->captureStripeCheckoutParams();

        $masterclass = Product::where('slug', 'nativephp-masterclass')->firstOrFail();
        $masterclass->prices()->update(['stripe_price_id' => 'price_test123']);

        $this
            ->actingAs(User::factory()->create(['stripe_id' => 'cus_test123']))
            ->post(route('course.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertDatabaseCount('carts', 0);

        $invoiceMetadata = $captured->params['invoice_creation']['invoice_data']['metadata'];

        $this->assertSame((string) $masterclass->id, $invoiceMetadata['product_id']);
        $this->assertArrayNotHasKey('cart_id', $invoiceMetadata);
    }

    #[Test]
    public function course_checkout_is_not_found_when_the_masterclass_is_inactive(): void
    {
        Product::where('slug', 'nativephp-masterclass')->update(['is_active' => false]);

        $this
            ->actingAs(User::factory()->create())
            ->post(route('course.checkout'))
            ->assertNotFound();
    }

    #[Test]
    public function buying_the_masterclass_does_not_license_other_items_in_the_cart(): void
    {
        Notification::fake();

        $captured = $this->captureStripeCheckoutParams();

        $masterclass = Product::where('slug', 'nativephp-masterclass')->firstOrFail();
        $masterclass->prices()->update(['stripe_price_id' => 'price_test123']);

        $buyer = User::factory()->create(['stripe_id' => 'cus_test123']);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $plugin->id]);

        $cartService = resolve(CartService::class);
        $cart = $cartService->getCart($buyer);
        $pluginItem = $cartService->addPlugin($cart, $plugin);

        $this
            ->actingAs($buyer)
            ->post(route('course.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertSame([['price' => 'price_test123', 'quantity' => 1]], $captured->params['line_items']);

        // Stripe copies the session's invoice metadata onto the invoice it reports as paid.
        (new HandleInvoicePaidJob(Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $buyer->stripe_id,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'total' => 29900,
            'metadata' => $captured->params['invoice_creation']['invoice_data']['metadata'],
            'lines' => [],
        ])))->handle();

        $this->assertTrue($masterclass->isOwnedBy($buyer));
        Notification::assertSentTo($buyer, PurchaseReceipt::class);

        $this->assertDatabaseCount('plugin_payouts', 0);
        Notification::assertNothingSentTo($developerAccount->user);
        $this->assertDatabaseCount('plugin_licenses', 0);
        $this->assertFalse($buyer->hasPluginAccess($plugin));

        // The buyer's cart is exactly as they left it.
        $this->assertNull($cart->fresh()->completed_at);
        $this->assertSame([$pluginItem->id], $cart->items()->pluck('id')->all());
    }

    #[Test]
    public function course_checkout_returns_error_when_price_id_not_configured(): void
    {
        config(['services.stripe.course_price_id_199' => null]);

        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('course.checkout'))
            ->assertRedirect(route('course'))
            ->assertSessionHas('error', 'Course checkout is not configured yet.');
    }

    #[Test]
    public function course_checkout_uses_299_price_after_deadline(): void
    {
        Carbon::setTestNow('2026-06-15 00:00:01');
        config(['services.stripe.course_price_id_299' => 'price_299_test']);

        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $capturedParams = null;

        $mockCheckoutSessions = new class('https://checkout.stripe.com/test', $capturedParams)
        {
            public function __construct(
                private string $url,
                private &$capturedParams,
            ) {}

            public function create(array $params): Session
            {
                $this->capturedParams = $params;

                return Session::constructFrom([
                    'id' => 'cs_test123',
                    'url' => $this->url,
                ]);
            }
        };

        $mockCheckout = new \stdClass;
        $mockCheckout->sessions = $mockCheckoutSessions;

        $mockCustomers = new class
        {
            public function retrieve(): Customer
            {
                return Customer::constructFrom([
                    'id' => 'cus_test123',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->checkout = $mockCheckout;
        $mockStripeClient->customers = $mockCustomers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $this
            ->actingAs($user)
            ->post(route('course.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test');

        $this->assertNotNull($capturedParams);
        $this->assertArrayHasKey('line_items', $capturedParams);
        $this->assertEquals('price_299_test', $capturedParams['line_items'][0]['price']);

        Carbon::setTestNow();
    }

    #[Test]
    public function course_checkout_returns_error_when_299_price_id_not_configured_after_deadline(): void
    {
        Carbon::setTestNow('2026-06-15 00:00:01');
        config([
            'services.stripe.course_price_id_199' => 'price_199',
            'services.stripe.course_price_id_299' => null,
        ]);

        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('course.checkout'))
            ->assertRedirect(route('course'))
            ->assertSessionHas('error', 'Course checkout is not configured yet.');

        Carbon::setTestNow();
    }
}
