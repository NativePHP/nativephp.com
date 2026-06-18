<?php

namespace Tests\Feature\Livewire;

use App\Enums\Subscription;
use App\Livewire\OrderSuccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Cashier;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Collection;
use Stripe\Exception\InvalidRequestException;
use Stripe\LineItem;
use Stripe\StripeClient;
use Tests\TestCase;

class OrderSuccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStripeClient();
    }

    #[Test]
    public function it_renders_successfully()
    {
        $response = $this->withoutVite()->get('/order/cs_test_123');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_displays_loading_state_when_subscription_record_is_not_yet_available()
    {
        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', null)
            ->assertSet('subscription', null)
            ->assertSee('Finalising your subscription')
            ->assertSeeHtml('wire:poll.2s="loadData"');
    }

    #[Test]
    public function it_shows_dashboard_link_for_existing_users(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'stripe_id' => 'cus_test123',
            'email_verified_at' => now(),
        ]);

        $this->buildSubscriptionRecord($user);

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'test@example.com')
            ->assertSet('isExistingUser', true)
            ->assertSee('Go to Dashboard')
            ->assertSeeHtml(route('dashboard'))
            ->assertDontSee('claim your account')
            ->assertDontSee('Finalising your subscription');
    }

    #[Test]
    public function it_shows_claim_message_for_new_users(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'test@example.com',
            'stripe_id' => 'cus_test123',
        ]);

        $this->buildSubscriptionRecord($user);

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'test@example.com')
            ->assertSet('isExistingUser', false)
            ->assertSee('claim your account')
            ->assertSee('test@example.com')
            ->assertSee('support@nativephp.com')
            ->assertDontSee('Go to Dashboard');
    }

    #[Test]
    public function it_polls_until_subscription_record_appears(): void
    {
        $component = Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('subscription', null)
            ->assertSee('Finalising your subscription')
            ->assertSeeHtml('wire:poll.2s="loadData"');

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'stripe_id' => 'cus_test123',
            'email_verified_at' => now(),
        ]);

        $this->buildSubscriptionRecord($user);

        $component->call('loadData')
            ->assertSet('subscription', Subscription::Max)
            ->assertSee('Go to Dashboard')
            ->assertDontSee('Finalising your subscription');
    }

    private function buildSubscriptionRecord(User $user): void
    {
        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user, 'user')
            ->create(['stripe_id' => 'sub_test123']);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_id' => 'si_test123',
                'stripe_price' => Subscription::Max->stripePriceId(),
            ]);
    }

    #[Test]
    public function it_redirects_to_mobile_route_when_checkout_session_is_not_found()
    {
        $mockStripeClient = $this->createMock(StripeClient::class);

        $mockStripeClient->checkout = new class {};

        $mockStripeClient->checkout->sessions = new class
        {
            public function retrieve()
            {
                throw new InvalidRequestException('No such checkout.session');
            }

            public function allLineItems()
            {
                throw new InvalidRequestException('No such checkout.session');
            }
        };

        $this->app->bind(StripeClient::class, function ($app, $parameters) use ($mockStripeClient) {
            return $mockStripeClient;
        });

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'not_a_real_checkout_session'])
            ->assertRedirect('/mobile');
    }

    private function mockStripeClient(): void
    {
        $mockCheckoutSession = CheckoutSession::constructFrom([
            'id' => 'cs_test_123',
            'customer' => 'cus_test123',
            'customer_details' => [
                'email' => 'test@example.com',
            ],
            'subscription' => 'sub_test123',
        ]);

        $mockCheckoutSessionLineItems = Collection::constructFrom([
            'object' => 'list',
            'data' => [
                LineItem::constructFrom([
                    'id' => 'li_1RFKPpAyFo6rlwXqAHI9wA95',
                    'object' => 'item',
                    'description' => 'Max',
                    'price' => [
                        'id' => Subscription::Max->stripePriceId(),
                        'object' => 'price',
                        'product' => 'prod_S9Z5CgycbP7P4y',
                    ],
                ]),
            ],
        ]);

        $mockStripeClient = $this->createMock(StripeClient::class);

        $mockStripeClient->checkout = new class($mockCheckoutSession)
        {
            private $mockCheckoutSession;

            public function __construct($mockCheckoutSession)
            {
                $this->mockCheckoutSession = $mockCheckoutSession;
            }
        };

        $mockStripeClient->checkout->sessions = new class($mockCheckoutSession, $mockCheckoutSessionLineItems)
        {
            private $mockCheckoutSession;

            private $mockCheckoutSessionLineItems;

            public function __construct($mockCheckoutSession, $mockCheckoutSessionLineItems)
            {
                $this->mockCheckoutSession = $mockCheckoutSession;
                $this->mockCheckoutSessionLineItems = $mockCheckoutSessionLineItems;
            }

            public function retrieve()
            {
                return $this->mockCheckoutSession;
            }

            public function allLineItems()
            {
                return $this->mockCheckoutSessionLineItems;
            }
        };

        $this->app->bind(StripeClient::class, function ($app, $parameters) use ($mockStripeClient) {
            return $mockStripeClient;
        });
    }
}
