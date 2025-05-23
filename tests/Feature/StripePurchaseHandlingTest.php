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
    public function a_user_is_not_created_when_a_stripe_customer_is_created()
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

        Bus::assertNotDispatched(CreateUserFromStripeCustomer::class);
    }

    #[Test]
    public function a_user_is_created_when_a_stripe_customer_subscription_is_created_and_a_matching_user_doesnt_exist()
    {
        Bus::fake();

        $this->mockStripeClient();

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

        Bus::assertDispatched(CreateUserFromStripeCustomer::class);
    }

    #[Test]
    public function a_user_is_not_created_when_a_stripe_customer_subscription_is_created_if_a_matching_user_already_exists()
    {
        Bus::fake();

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
                    'customer' => $user->stripe_id,
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

        Bus::assertNotDispatched(CreateUserFromStripeCustomer::class);
    }

    #[Test]
    public function a_license_is_not_created_when_a_stripe_subscription_is_created()
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

        Bus::assertNotDispatched(CreateAnystackLicenseJob::class);

        $user->refresh();

        $this->assertNotEmpty($user->subscriptions);
        $this->assertNotEmpty($user->subscriptions->first()->items);
    }

    #[Test]
    public function a_license_is_created_when_a_stripe_invoice_is_paid()
    {
        Bus::fake([CreateAnystackLicenseJob::class]);

        $user = User::factory()->create([
            'stripe_id' => 'cus_test123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        \Laravel\Cashier\Subscription::factory()
            ->for($user, 'user')
            ->create([
                'stripe_id' => 'sub_test123',
                'stripe_status' => 'incomplete', // the subscription is incomplete at the time this webhook is sent
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);
        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($user->subscriptions->first(), 'subscription')
            ->create([
                'stripe_id' => 'si_test',
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        $this->mockStripeClient($user);

        $payload = [
            'id' => 'evt_test_webhook',
            'type' => 'invoice.paid',
            'data' => [
                'object' => [
                    'id' => 'in_test',
                    'object' => 'invoice',
                    'billing_reason' => 'subscription_create',
                    'customer' => 'cus_test123',
                    'paid' => true,
                    'status' => 'paid',
                    'lines' => [
                        'object' => 'list',
                        'data' => [
                            [
                                'id' => 'il_test',
                                'price' => [
                                    'id' => Subscription::Max->stripePriceId(),
                                    'object' => 'price',
                                    'product' => 'prod_test',
                                ],
                                'quantity' => 1,
                                'subscription' => 'sub_test123',
                                'subscription_item' => 'si_test',
                                'type' => 'subscription',
                            ],
                        ],
                    ],
                    'subscription' => 'sub_test123',
                ],
            ],
        ];

        $this->postJson('/stripe/webhook', $payload);

        Bus::assertDispatched(CreateAnystackLicenseJob::class, function (CreateAnystackLicenseJob $job) {
            return $job->user->email === 'john@example.com' &&
                   $job->subscription === Subscription::Max &&
                   $job->subscriptionItemId === $job->user->subscriptions->first()->items()->first()->id &&
                   $job->firstName === 'John' &&
                   $job->lastName === 'Doe';
        });
    }

    protected function mockStripeClient(?User $user = null): void
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
                    'id' => $this->user?->stripe_id ?: 'cus_test123',
                    'name' => $this->user?->name ?: 'Test Customer',
                    'email' => $this->user?->email ?: 'test@example.com',
                ]);
            }
        };

        $this->app->bind(StripeClient::class, function ($app, $parameters) use ($mockStripeClient) {
            return $mockStripeClient;
        });
    }
}
