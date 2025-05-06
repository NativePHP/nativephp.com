<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Jobs\CreateAnystackLicenseJob;
use App\Jobs\CreateUserFromStripeCustomer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Customer;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePurchaseHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cashier.webhook.secret', null);

        Http::fake([
            'https://api.anystack.sh/v1/contacts' => Http::response(['data' => ['id' => 'contact-123']], 200),
            'https://api.anystack.sh/v1/products/*/licenses' => Http::response(['data' => ['key' => 'test-license-key-12345']], 200),
        ]);
    }

    #[Test]
    public function a_user_is_created_when_a_stripe_customer_is_created()
    {
        Bus::fake();

        $payload = [
            'id' => 'evt_test_webhook',
            'type' => 'customer.created',
            'data' => [
                'object' => [
                    'id' => 'cus_test123',
                    'name' => 'Test Customer',
                    'email' => 'test@example.com',
                ],
            ],
        ];

        $this->postJson('/stripe/webhook', $payload);

        Bus::assertDispatched(CreateUserFromStripeCustomer::class);
    }

    #[Test]
    public function a_license_is_created_when_a_stripe_subscription_is_created()
    {
        Bus::fake([CreateAnystackLicenseJob::class]);

        $user = User::factory()->create([
            'stripe_id' => 'cus_test123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->mockStripeClient($user);

        $payload = [
            'id' => 'evt_test_webhook',
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_test123',
                    'customer' => 'cus_test123',
                    'status' => 'active',
                    'items' => [
                        'object' => 'list',
                        'data' => [
                            [
                                'id' => 'si_test',
                                'price' => [
                                    'id' => Subscription::Max->stripePriceId(),
                                    'product' => 'prod_test',
                                ],
                                'quantity' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->postJson('/stripe/webhook', $payload);

        Bus::assertDispatched(CreateAnystackLicenseJob::class, function ($job) {
            return $job->email === 'john@example.com' &&
                   $job->subscription === Subscription::Max &&
                   $job->firstName === 'John' &&
                   $job->lastName === 'Doe';
        });

        $user->refresh();

        $this->assertNotEmpty($user->subscriptions);
        $this->assertNotEmpty($user->subscriptions->first()->items);
    }

    protected function mockStripeClient(User $user): void
    {
        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->customers = new class($user)
        {
            private $user;

            public function __construct($user)
            {
                $this->user = $user;
            }

            public function retrieve()
            {
                return Customer::constructFrom([
                    'id' => $this->user->stripe_id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ]);
            }
        };

        $this->app->instance(StripeClient::class, $mockStripeClient);
    }
}
