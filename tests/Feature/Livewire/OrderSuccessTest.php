<?php

namespace Tests\Feature\Livewire;

use App\Livewire\OrderSuccess;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\StripeClient;
use Tests\TestCase;

class OrderSuccessTest extends TestCase
{
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
    public function it_displays_license_key_when_available()
    {
        Session::flush();

        Cache::put('test@example.com.license_key', 'test-license-key-12345');

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'test@example.com')
            ->assertSet('licenseKey', 'test-license-key-12345')
            ->assertSee('test-license-key-12345')
            ->assertSee('test@example.com')
            ->assertDontSee('License registration in progress');
    }

    #[Test]
    public function it_uses_session_data_when_available()
    {
        Session::put('customer_email', 'session@example.com');
        Session::put('license_key', 'session-license-key');

        Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('email', 'session@example.com')
            ->assertSet('licenseKey', 'session-license-key')
            ->assertSee('session-license-key')
            ->assertSee('session@example.com');
    }

    #[Test]
    public function it_polls_for_updates()
    {
        Session::flush();

        $component = Livewire::test(OrderSuccess::class, ['checkoutSessionId' => 'cs_test_123'])
            ->assertSet('licenseKey', null)
            ->assertSee('License registration in progress')
            ->assertSeeHtml('wire:poll.2s="loadData"');

        Cache::put('test@example.com.license_key', 'polled-license-key');

        $component->call('loadData')
            ->assertSet('licenseKey', 'polled-license-key')
            ->assertSee('polled-license-key')
            ->assertDontSee('License registration in progress');
    }

    private function mockStripeClient(): void
    {
        $mockCheckoutSession = CheckoutSession::constructFrom([
            'id' => 'cs_test_123',
            'customer_details' => [
                'email' => 'test@example.com',
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

        $mockStripeClient->checkout->sessions = new class($mockCheckoutSession)
        {
            private $mockCheckoutSession;

            public function __construct($mockCheckoutSession)
            {
                $this->mockCheckoutSession = $mockCheckoutSession;
            }

            public function retrieve()
            {
                return $this->mockCheckoutSession;
            }
        };

        $this->app->instance(StripeClient::class, $mockStripeClient);
    }
}
