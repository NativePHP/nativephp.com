<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CreateAnystackLicenseJob;
use App\Jobs\HandleInvoicePaidJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Laravel\Cashier\SubscriptionItem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Invoice;
use Stripe\Service\SubscriptionService;
use Stripe\StripeClient;
use Stripe\Subscription;
use Tests\TestCase;

class HandleInvoicePaidJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('subscriptionPlanProvider')]
    public function it_does_not_create_license_for_any_subscription(string $planKey): void
    {
        Bus::fake();

        $user = User::factory()->create([
            'stripe_id' => 'cus_test123',
        ]);

        $priceId = 'price_test_'.$planKey;
        config(["subscriptions.plans.{$planKey}.stripe_price_id" => $priceId]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user, 'user')
            ->create([
                'stripe_id' => 'sub_test123',
                'stripe_status' => 'active',
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_id' => 'si_test123',
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        $this->mockStripeSubscriptionRetrieve('sub_test123');

        $invoice = $this->createStripeInvoice(
            customerId: 'cus_test123',
            subscriptionId: 'sub_test123',
            billingReason: Invoice::BILLING_REASON_SUBSCRIPTION_CREATE,
            priceId: $priceId,
            subscriptionItemId: 'si_test123',
        );

        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        Bus::assertNotDispatched(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function it_does_not_auto_set_is_comped_when_invoice_total_is_zero(): void
    {
        Bus::fake();

        $user = User::factory()->create([
            'stripe_id' => 'cus_test123',
        ]);

        $priceId = 'price_test_mini';
        config(['subscriptions.plans.mini.stripe_price_id' => $priceId]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user, 'user')
            ->create([
                'stripe_id' => 'sub_test123',
                'stripe_status' => 'active',
                'stripe_price' => $priceId,
                'quantity' => 1,
                'is_comped' => false,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_id' => 'si_test123',
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        $this->mockStripeSubscriptionRetrieve('sub_test123');

        $invoice = $this->createStripeInvoice(
            customerId: 'cus_test123',
            subscriptionId: 'sub_test123',
            billingReason: Invoice::BILLING_REASON_SUBSCRIPTION_CREATE,
            priceId: $priceId,
            subscriptionItemId: 'si_test123',
            total: 0,
        );

        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $subscription->refresh();

        $this->assertFalse((bool) $subscription->is_comped);
        $this->assertEquals(0, $subscription->price_paid);
    }

    public static function subscriptionPlanProvider(): array
    {
        return [
            'mini' => ['mini'],
            'pro' => ['pro'],
            'max' => ['max'],
        ];
    }

    private function createStripeInvoice(
        string $customerId,
        string $subscriptionId,
        string $billingReason,
        string $priceId,
        string $subscriptionItemId,
        int $total = 25000,
    ): Invoice {
        return Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'object' => 'invoice',
            'customer' => $customerId,
            'subscription' => $subscriptionId,
            'billing_reason' => $billingReason,
            'total' => $total,
            'currency' => 'usd',
            'payment_intent' => 'pi_test_'.uniqid(),
            'metadata' => [],
            'lines' => [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'il_test_'.uniqid(),
                        'object' => 'line_item',
                        'subscription_item' => $subscriptionItemId,
                        'price' => [
                            'id' => $priceId,
                            'object' => 'price',
                            'active' => true,
                            'currency' => 'usd',
                            'unit_amount' => 25000,
                        ],
                    ],
                ],
                'has_more' => false,
                'total_count' => 1,
            ],
        ]);
    }

    private function mockStripeSubscriptionRetrieve(string $subscriptionId): void
    {
        $mockSubscription = Subscription::constructFrom([
            'id' => $subscriptionId,
            'metadata' => [],
            'current_period_end' => now()->addYear()->timestamp,
        ]);

        $mockSubscriptionsService = $this->createMock(SubscriptionService::class);
        $mockSubscriptionsService->method('retrieve')->willReturn($mockSubscription);

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->subscriptions = $mockSubscriptionsService;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);
    }
}
