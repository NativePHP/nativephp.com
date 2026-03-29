<?php

namespace Tests\Feature;

use App\Enums\PayoutStatus;
use App\Jobs\HandleInvoicePaidJob;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Invoice;
use Tests\TestCase;

class MaxSubscriberPayoutTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_1RoZk0AyFo6rlwXqjkLj4hZ0';

    private const PRO_PRICE_ID = 'price_1RoZeVAyFo6rlwXqtnOViUCf';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    private function createStripeInvoice(string $cartId, string $customerId): Invoice
    {
        $invoice = Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $customerId,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'metadata' => ['cart_id' => $cartId],
            'lines' => [],
        ]);

        return $invoice;
    }

    private function createSubscription(User $user, string $priceId): Subscription
    {
        return Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => $priceId]);
    }

    #[Test]
    public function max_subscriber_gets_zero_platform_fee_for_third_party_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(2999, $payout->developer_amount);
        $this->assertEquals(PayoutStatus::Pending, $payout->status);
    }

    #[Test]
    public function non_max_subscriber_gets_normal_platform_fee_for_third_party_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::PRO_PRICE_ID);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
    }

    #[Test]
    public function non_subscriber_gets_normal_platform_fee_for_third_party_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
    }

    #[Test]
    public function max_subscriber_gets_normal_platform_fee_for_official_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => true,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
    }
}
