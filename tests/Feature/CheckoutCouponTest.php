<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Checkout\Session;
use Stripe\Coupon;
use Stripe\Customer;
use Stripe\StripeClient;
use Tests\TestCase;

class CheckoutCouponTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Bind a mocked StripeClient and return a holder object whose ->params
     * property captures the checkout session params sent to Stripe. The coupons
     * endpoint serves $coupon, or throws when null to simulate a bad coupon ID.
     */
    private function captureStripeCheckoutParams(?array $coupon = [
        'id' => 'coupon_test123',
        'valid' => true,
        'amount_off' => 10000,
        'percent_off' => null,
    ]): \stdClass
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

        $mockCoupons = new class($coupon)
        {
            public function __construct(private ?array $coupon) {}

            public function retrieve(): Coupon
            {
                if ($this->coupon === null) {
                    throw new \RuntimeException('No such coupon');
                }

                return Coupon::constructFrom($this->coupon);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->checkout = $mockCheckout;
        $mockStripeClient->customers = $mockCustomers;
        $mockStripeClient->coupons = $mockCoupons;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        return $captured;
    }

    private function createSubscriber(): User
    {
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => 'price_test_pro']);

        return $user;
    }

    #[Test]
    public function subscriber_tier_price_wins_amount_tie_over_regular_tier(): void
    {
        $product = Product::factory()->active()->create();
        ProductPrice::factory()->for($product)->regular()->amount(29900)->create();
        $subscriberPrice = ProductPrice::factory()
            ->for($product)
            ->subscriber()
            ->amount(29900)
            ->withCoupon('coupon_test123')
            ->create();

        $bestPrice = $product->getBestPriceForUser($this->createSubscriber());

        $this->assertTrue($bestPrice->is($subscriberPrice));
        $this->assertSame('coupon_test123', $bestPrice->stripe_coupon_id);
    }

    #[Test]
    public function guest_resolves_regular_tier_price_without_coupon(): void
    {
        $product = Product::factory()->active()->create();
        $regularPrice = ProductPrice::factory()->for($product)->regular()->amount(29900)->create();
        ProductPrice::factory()
            ->for($product)
            ->subscriber()
            ->amount(29900)
            ->withCoupon('coupon_test123')
            ->create();

        $bestPrice = $product->getBestPriceForUser(null);

        $this->assertTrue($bestPrice->is($regularPrice));
        $this->assertNull($bestPrice->stripe_coupon_id);
    }

    #[Test]
    public function discounted_amount_deducts_coupon_amount_off_from_stripe(): void
    {
        $this->captureStripeCheckoutParams();

        $price = ProductPrice::factory()->subscriber()->amount(29900)->withCoupon('coupon_test123')->create();

        $this->assertSame(19900, $price->discountedAmount());
        $this->assertSame('199', $price->discountedDisplayAmount());
    }

    #[Test]
    public function discounted_amount_supports_percent_off_coupons(): void
    {
        $this->captureStripeCheckoutParams([
            'id' => 'coupon_test123',
            'valid' => true,
            'amount_off' => null,
            'percent_off' => 50.0,
        ]);

        $price = ProductPrice::factory()->subscriber()->amount(29900)->withCoupon('coupon_test123')->create();

        $this->assertSame(14950, $price->discountedAmount());
        $this->assertSame('149.50', $price->discountedDisplayAmount());
    }

    #[Test]
    public function discounted_amount_falls_back_to_full_amount_when_coupon_cannot_be_retrieved(): void
    {
        $this->captureStripeCheckoutParams(null);

        $price = ProductPrice::factory()->subscriber()->amount(29900)->withCoupon('coupon_missing')->create();

        $this->assertSame(29900, $price->discountedAmount());
    }

    #[Test]
    public function course_checkout_pre_applies_coupon_for_subscribers(): void
    {
        Carbon::setTestNow('2026-06-14 23:59:59');
        config(['services.stripe.course_price_id_199' => 'price_test123']);

        $captured = $this->captureStripeCheckoutParams();
        $subscriber = $this->createSubscriber();

        $masterclass = Product::where('slug', 'nativephp-masterclass')->firstOrFail();
        $masterclass->prices()->update(['amount' => 29900]);
        ProductPrice::factory()
            ->for($masterclass)
            ->subscriber()
            ->amount(29900)
            ->withCoupon('coupon_test123')
            ->create();

        $this->actingAs($subscriber)
            ->post(route('course.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertNotNull($captured->params, 'Stripe checkout session should have been created');
        $this->assertSame([['coupon' => 'coupon_test123']], $captured->params['discounts']);
        $this->assertArrayNotHasKey('allow_promotion_codes', $captured->params);

        Carbon::setTestNow();
    }

    #[Test]
    public function course_checkout_keeps_manual_promotion_codes_for_non_subscribers(): void
    {
        Carbon::setTestNow('2026-06-14 23:59:59');
        config(['services.stripe.course_price_id_199' => 'price_test123']);

        $captured = $this->captureStripeCheckoutParams();
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $masterclass = Product::where('slug', 'nativephp-masterclass')->firstOrFail();
        $masterclass->prices()->update(['amount' => 29900]);
        ProductPrice::factory()
            ->for($masterclass)
            ->subscriber()
            ->amount(29900)
            ->withCoupon('coupon_test123')
            ->create();

        $this->actingAs($user)
            ->post(route('course.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertNotNull($captured->params, 'Stripe checkout session should have been created');
        $this->assertTrue($captured->params['allow_promotion_codes']);
        $this->assertArrayNotHasKey('discounts', $captured->params);

        Carbon::setTestNow();
    }

    #[Test]
    public function cart_checkout_pre_applies_coupon_from_product_price(): void
    {
        $captured = $this->captureStripeCheckoutParams();
        $subscriber = $this->createSubscriber();

        $product = Product::factory()->active()->create();
        ProductPrice::factory()->for($product)->regular()->amount(29900)->create();
        ProductPrice::factory()
            ->for($product)
            ->subscriber()
            ->amount(29900)
            ->withCoupon('coupon_test123')
            ->create();

        $cartService = resolve(CartService::class);
        $cart = $cartService->getCart($subscriber);
        $cartService->addProduct($cart, $product);

        $this->actingAs($subscriber)
            ->post(route('cart.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertNotNull($captured->params, 'Stripe checkout session should have been created');
        $this->assertSame([['coupon' => 'coupon_test123']], $captured->params['discounts']);
        $this->assertArrayNotHasKey('allow_promotion_codes', $captured->params);

        // The full price is charged; Stripe deducts the coupon's discount itself.
        $this->assertSame(29900, $captured->params['line_items'][0]['price_data']['unit_amount']);
    }

    #[Test]
    public function cart_checkout_keeps_manual_promotion_codes_when_none_pre_applied(): void
    {
        $captured = $this->captureStripeCheckoutParams();
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $product = Product::factory()->active()->create();
        ProductPrice::factory()->for($product)->regular()->amount(29900)->create();

        $cartService = resolve(CartService::class);
        $cart = $cartService->getCart($user);
        $cartService->addProduct($cart, $product);

        $this->actingAs($user)
            ->post(route('cart.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertNotNull($captured->params, 'Stripe checkout session should have been created');
        $this->assertTrue($captured->params['allow_promotion_codes']);
        $this->assertArrayNotHasKey('discounts', $captured->params);
    }

    #[Test]
    public function course_checkout_uses_stripe_price_id_from_the_database_over_config(): void
    {
        Carbon::setTestNow('2026-06-14 23:59:59');
        config(['services.stripe.course_price_id_199' => 'price_env_config']);

        $captured = $this->captureStripeCheckoutParams();
        $subscriber = $this->createSubscriber();

        $masterclass = Product::where('slug', 'nativephp-masterclass')->firstOrFail();
        $masterclass->prices()->update(['amount' => 29900]);
        ProductPrice::factory()
            ->for($masterclass)
            ->subscriber()
            ->amount(29900)
            ->withStripePrice('price_db_override')
            ->create();

        $this->actingAs($subscriber)
            ->post(route('course.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertNotNull($captured->params, 'Stripe checkout session should have been created');
        $this->assertSame('price_db_override', $captured->params['line_items'][0]['price']);
    }

    #[Test]
    public function cart_checkout_uses_stripe_price_line_item_when_price_is_backed_by_stripe(): void
    {
        $captured = $this->captureStripeCheckoutParams();
        $subscriber = $this->createSubscriber();

        $product = Product::factory()->active()->create();
        ProductPrice::factory()->for($product)->regular()->amount(29900)->create();
        ProductPrice::factory()
            ->for($product)
            ->subscriber()
            ->amount(29900)
            ->withStripePrice('price_backed')
            ->withCoupon('coupon_test123')
            ->create();

        $cartService = resolve(CartService::class);
        $cart = $cartService->getCart($subscriber);
        $cartService->addProduct($cart, $product);

        $this->actingAs($subscriber)
            ->post(route('cart.checkout'))
            ->assertRedirect('https://checkout.stripe.com/test-session');

        $this->assertNotNull($captured->params, 'Stripe checkout session should have been created');
        $this->assertSame('price_backed', $captured->params['line_items'][0]['price']);
        $this->assertArrayNotHasKey('price_data', $captured->params['line_items'][0]);
        $this->assertSame([['coupon' => 'coupon_test123']], $captured->params['discounts']);
    }
}
