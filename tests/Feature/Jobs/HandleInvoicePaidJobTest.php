<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CreateAnystackLicenseJob;
use App\Jobs\HandleInvoicePaidJob;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use App\Notifications\PurchaseReceipt;
use App\Notifications\UltraSubscriptionStarted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
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

    #[Test]
    public function it_sends_ultra_welcome_email_when_an_ultra_subscription_is_created(): void
    {
        Notification::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $priceId = 'price_test_max';
        config(['subscriptions.plans.max.stripe_price_id' => $priceId]);

        $this->mockStripeSubscriptionRetrieve('sub_test123');

        $invoice = $this->createStripeInvoice(
            customerId: 'cus_test123',
            subscriptionId: 'sub_test123',
            billingReason: Invoice::BILLING_REASON_SUBSCRIPTION_CREATE,
            priceId: $priceId,
            subscriptionItemId: 'si_test123',
        );

        (new HandleInvoicePaidJob($invoice))->handle();

        Notification::assertSentTo($user, UltraSubscriptionStarted::class);
    }

    #[Test]
    public function it_does_not_send_ultra_welcome_email_for_non_ultra_subscriptions(): void
    {
        Notification::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $priceId = 'price_test_pro';
        config(['subscriptions.plans.pro.stripe_price_id' => $priceId]);

        $this->mockStripeSubscriptionRetrieve('sub_test123');

        $invoice = $this->createStripeInvoice(
            customerId: 'cus_test123',
            subscriptionId: 'sub_test123',
            billingReason: Invoice::BILLING_REASON_SUBSCRIPTION_CREATE,
            priceId: $priceId,
            subscriptionItemId: 'si_test123',
        );

        (new HandleInvoicePaidJob($invoice))->handle();

        Notification::assertNothingSentTo($user);
    }

    #[Test]
    public function it_sends_a_purchase_receipt_to_the_buyer_after_a_cart_purchase(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);

        $plugin = Plugin::factory()->approved()->free()->create(['is_active' => true]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'price_at_addition' => 0,
        ]);

        $invoice = Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $buyer->stripe_id,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'metadata' => ['cart_id' => $cart->id],
            'lines' => [],
        ]);

        (new HandleInvoicePaidJob($invoice))->handle();

        Notification::assertSentTo($buyer, PurchaseReceipt::class);
    }

    #[Test]
    public function it_only_licenses_cart_items_recorded_in_the_invoice_snapshot(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);

        $purchasedPlugin = Plugin::factory()->approved()->create(['is_active' => true]);

        $leftoverPlugin = Plugin::factory()->approved()->create(['is_active' => true]);
        $leftoverBundle = PluginBundle::factory()->create();
        $leftoverBundle->plugins()->attach($leftoverPlugin->id, ['sort_order' => 1]);

        $cart = Cart::factory()->for($buyer)->create();

        $purchasedItem = CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $purchasedPlugin->id,
            'price_at_addition' => 2500,
        ]);

        // This bundle is still sitting in the cart but was never part of the checkout.
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_bundle_id' => $leftoverBundle->id,
            'bundle_price_at_addition' => 9999,
        ]);

        $invoice = Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $buyer->stripe_id,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'metadata' => [
                'cart_id' => (string) $cart->id,
                'cart_item_ids' => (string) $purchasedItem->id,
            ],
            'lines' => [],
        ]);

        (new HandleInvoicePaidJob($invoice))->handle();

        $this->assertDatabaseHas('plugin_licenses', [
            'user_id' => $buyer->id,
            'plugin_id' => $purchasedPlugin->id,
            'plugin_bundle_id' => null,
            'price_paid' => 2500,
        ]);

        $this->assertDatabaseMissing('plugin_licenses', [
            'plugin_bundle_id' => $leftoverBundle->id,
        ]);

        $this->assertDatabaseMissing('plugin_licenses', [
            'plugin_id' => $leftoverPlugin->id,
        ]);

        $this->assertEquals(1, $buyer->pluginLicenses()->count());
        $this->assertNotNull($cart->fresh()->completed_at);
    }

    #[Test]
    public function it_licenses_a_product_bought_outside_the_cart(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);
        $product = Product::factory()->active()->create();

        $invoice = $this->createProductInvoice($buyer, $product->id, total: 19900);

        (new HandleInvoicePaidJob($invoice))->handle();

        $this->assertDatabaseHas('product_licenses', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'stripe_invoice_id' => $invoice->id,
            'stripe_payment_intent_id' => $invoice->payment_intent,
            'price_paid' => 19900,
            'currency' => 'USD',
        ]);

        Notification::assertSentToTimes($buyer, PurchaseReceipt::class, 1);
    }

    #[Test]
    public function it_does_not_license_a_directly_bought_product_twice(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);
        $product = Product::factory()->active()->create();

        $invoice = $this->createProductInvoice($buyer, $product->id);

        (new HandleInvoicePaidJob($invoice))->handle();
        (new HandleInvoicePaidJob($invoice))->handle();

        // A second payment for a product the buyer already owns has nothing left to grant.
        (new HandleInvoicePaidJob($this->createProductInvoice($buyer, $product->id)))->handle();

        $this->assertSame($invoice->id, $buyer->productLicenses()->sole()->stripe_invoice_id);
        Notification::assertSentToTimes($buyer, PurchaseReceipt::class, 1);
    }

    #[Test]
    public function it_takes_a_directly_bought_product_out_of_the_buyers_cart(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);
        $product = Product::factory()->active()->create();
        $plugin = Plugin::factory()->approved()->create(['is_active' => true]);

        $cart = Cart::factory()->for($buyer)->create();

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_price_at_addition' => 29900,
        ]);

        $pluginItem = CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'price_at_addition' => 4900,
        ]);

        // Someone else has the same product waiting in their own cart.
        $otherBuyersItem = CartItem::create([
            'cart_id' => Cart::factory()->create()->id,
            'product_id' => $product->id,
            'product_price_at_addition' => 29900,
        ]);

        // A cart the buyer completed in the past keeps its record of the product.
        $completedCartItem = CartItem::create([
            'cart_id' => Cart::factory()->for($buyer)->create(['completed_at' => now()->subMonth()])->id,
            'product_id' => $product->id,
            'product_price_at_addition' => 29900,
        ]);

        (new HandleInvoicePaidJob($this->createProductInvoice($buyer, $product->id)))->handle();

        $this->assertTrue($product->isOwnedBy($buyer));

        $this->assertNull($cart->fresh()->completed_at);
        $this->assertSame([$pluginItem->id], $cart->items()->pluck('id')->all());
        $this->assertEquals(0, $buyer->pluginLicenses()->count());

        $this->assertModelExists($otherBuyersItem);
        $this->assertModelExists($completedCartItem);
    }

    #[Test]
    public function it_takes_a_product_the_buyer_already_owns_out_of_their_cart(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);
        $product = Product::factory()->active()->create();

        ProductLicense::factory()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);

        $cart = Cart::factory()->for($buyer)->create();

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_price_at_addition' => 29900,
        ]);

        (new HandleInvoicePaidJob($this->createProductInvoice($buyer, $product->id)))->handle();

        $this->assertEquals(0, $cart->items()->count());
        $this->assertEquals(1, $buyer->productLicenses()->count());
        Notification::assertNothingSentTo($buyer);
    }

    #[Test]
    public function it_licenses_nothing_for_a_direct_purchase_of_an_unknown_product(): void
    {
        Notification::fake();

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer']);

        (new HandleInvoicePaidJob($this->createProductInvoice($buyer, 999999)))->handle();

        $this->assertDatabaseCount('product_licenses', 0);
        Notification::assertNothingSentTo($buyer);
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

    private function createProductInvoice(User $buyer, int $productId, int $total = 29900): Invoice
    {
        return Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $buyer->stripe_id,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'total' => $total,
            'metadata' => ['product_id' => (string) $productId],
            'lines' => [],
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
