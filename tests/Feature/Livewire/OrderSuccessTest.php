<?php

namespace Tests\Feature\Livewire;

use App\Enums\Subscription;
use App\Livewire\OrderSuccess;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
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
    public function it_displays_loading_state_when_no_license_key_is_available()
    {
        Session::flush();

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'test@example.com')
            ->assertSet('licenseKey', null)
            ->assertSee('License registration in progress')
            ->assertSee('check your email');
    }

    #[Test]
    public function it_displays_license_key_when_available_in_database()
    {
        Session::flush();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        License::factory()->create([
            'user_id' => $user->id,
            'key' => 'db-license-key-12345',
            'policy_name' => 'max',
        ]);

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'test@example.com')
            ->assertSet('licenseKey', 'db-license-key-12345')
            ->assertSee('db-license-key-12345')
            ->assertSee('test@example.com')
            ->assertDontSee('License registration in progress');
    }

    #[Test]
    public function it_uses_session_data_when_available()
    {
        $checkoutSessionId = 'cs_test_123';

        Session::put("$checkoutSessionId.email", 'session@example.com');
        Session::put("$checkoutSessionId.license_key", 'session-license-key');

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'session@example.com')
            ->assertSet('licenseKey', 'session-license-key')
            ->assertSee('session-license-key')
            ->assertSee('session@example.com');
    }

    #[Test]
    public function it_polls_for_updates_from_database()
    {
        Session::flush();

        $component = Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('licenseKey', null)
            ->assertSee('License registration in progress')
            ->assertSeeHtml('wire:poll.2s="loadData"');

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        License::factory()->create([
            'user_id' => $user->id,
            'key' => 'db-polled-license-key',
            'policy_name' => 'max',
        ]);

        $component->call('loadData')
            ->assertSet('licenseKey', 'db-polled-license-key')
            ->assertSee('db-polled-license-key')
            ->assertDontSee('License registration in progress');
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
            'customer_details' => [
                'email' => 'test@example.com',
            ],
        ]);

        $mockCheckoutSessionLineItems = Collection::constructFrom([
            'object' => 'list',
            'data' => [
                LineItem::constructFrom([
                    'id' => 'li_1RFKPpAyFo6rlwXqAHI9wA95',
                    'object' => 'item',
                    'description' => 'Early Access Program (Max)',
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
