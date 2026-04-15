<?php

namespace Tests\Feature;

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

class DeveloperAccountPayoutTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_1RoZk0AyFo6rlwXqjkLj4hZ0';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    #[Test]
    public function payout_percentage_defaults_to_seventy(): void
    {
        $account = DeveloperAccount::factory()->create();

        $this->assertEquals(70, $account->payout_percentage);
    }

    #[Test]
    public function platform_fee_percent_is_inverse_of_payout_percentage(): void
    {
        $account = DeveloperAccount::factory()->create(['payout_percentage' => 80]);

        $this->assertEquals(20, $account->platformFeePercent());
    }

    #[Test]
    public function platform_fee_percent_with_default_payout_percentage(): void
    {
        $account = DeveloperAccount::factory()->create();

        $this->assertEquals(30, $account->platformFeePercent());
    }

    #[Test]
    public function custom_payout_percentage_is_used_for_plugin_purchase(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);

        $developerAccount = DeveloperAccount::factory()->create(['payout_percentage' => 80]);
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(10000)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 10000,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(10000, $payout->gross_amount);
        $this->assertEquals(2000, $payout->platform_fee);
        $this->assertEquals(8000, $payout->developer_amount);
    }

    #[Test]
    public function ultra_subscriber_overrides_custom_payout_percentage_to_full(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        Subscription::factory()->for($buyer)->active()->create(['stripe_price' => self::MAX_PRICE_ID]);

        $developerAccount = DeveloperAccount::factory()->create(['payout_percentage' => 80]);
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(10000)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 10000,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(10000, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(10000, $payout->developer_amount);
    }

    #[Test]
    public function developer_with_hundred_percent_payout_gets_full_amount(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);

        $developerAccount = DeveloperAccount::factory()->create(['payout_percentage' => 100]);
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(5000)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 5000,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(5000, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(5000, $payout->developer_amount);
    }

    private function createStripeInvoice(string $cartId, string $customerId): Invoice
    {
        return Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $customerId,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'metadata' => ['cart_id' => $cartId],
            'lines' => [],
        ]);
    }
}
