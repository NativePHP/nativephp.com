<?php

namespace Tests\Feature;

use App\Enums\PayoutStatus;
use App\Jobs\HandleInvoicePaidJob;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Invoice;
use Tests\TestCase;

class MultiDeveloperCheckoutPayoutTest extends TestCase
{
    use RefreshDatabase;

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

    private function createDeveloperWithPlugin(int $priceAmount, bool $isOfficial = false): array
    {
        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => $isOfficial,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount($priceAmount)->create(['plugin_id' => $plugin->id]);

        return [$developerAccount, $plugin];
    }

    #[Test]
    public function cart_with_individual_plugins_from_multiple_developers_creates_correct_payouts(): void
    {
        [$devAccountA, $pluginA] = $this->createDeveloperWithPlugin(2999);
        [$devAccountB, $pluginB] = $this->createDeveloperWithPlugin(4999);
        [$devAccountC, $pluginC] = $this->createDeveloperWithPlugin(1999);

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $cart = Cart::factory()->for($buyer)->create();

        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $pluginA->id,
            'plugin_price_id' => $pluginA->prices->first()->id,
            'price_at_addition' => 2999,
        ]);
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $pluginB->id,
            'plugin_price_id' => $pluginB->prices->first()->id,
            'price_at_addition' => 4999,
        ]);
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $pluginC->id,
            'plugin_price_id' => $pluginC->prices->first()->id,
            'price_at_addition' => 1999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        (new HandleInvoicePaidJob($invoice))->handle();

        $this->assertCount(3, PluginPayout::all());

        // Developer A: $29.99 plugin, 30% fee = $9.00, developer gets $20.99
        $payoutA = PluginPayout::where('developer_account_id', $devAccountA->id)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals(2999, $payoutA->gross_amount);
        $this->assertEquals(900, $payoutA->platform_fee);
        $this->assertEquals(2099, $payoutA->developer_amount);

        // Developer B: $49.99 plugin, 30% fee = $15.00, developer gets $34.99
        $payoutB = PluginPayout::where('developer_account_id', $devAccountB->id)->first();
        $this->assertNotNull($payoutB);
        $this->assertEquals(4999, $payoutB->gross_amount);
        $this->assertEquals(1500, $payoutB->platform_fee);
        $this->assertEquals(3499, $payoutB->developer_amount);

        // Developer C: $19.99 plugin, 30% fee = $6.00, developer gets $13.99
        $payoutC = PluginPayout::where('developer_account_id', $devAccountC->id)->first();
        $this->assertNotNull($payoutC);
        $this->assertEquals(1999, $payoutC->gross_amount);
        $this->assertEquals(600, $payoutC->platform_fee);
        $this->assertEquals(1399, $payoutC->developer_amount);
    }

    #[Test]
    public function bundle_with_plugins_from_multiple_developers_creates_proportional_payouts(): void
    {
        [$devAccountA, $pluginA] = $this->createDeveloperWithPlugin(3000);
        [$devAccountB, $pluginB] = $this->createDeveloperWithPlugin(5000);
        [$devAccountC, $pluginC] = $this->createDeveloperWithPlugin(2000);

        // Total retail value: 3000 + 5000 + 2000 = 10000
        // Bundle price: 7000 (30% discount)
        $bundle = PluginBundle::factory()->active()->create(['price' => 7000]);
        $bundle->plugins()->attach([
            $pluginA->id => ['sort_order' => 1],
            $pluginB->id => ['sort_order' => 2],
            $pluginC->id => ['sort_order' => 3],
        ]);

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $cart = Cart::factory()->for($buyer)->create();

        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_bundle_id' => $bundle->id,
            'bundle_price_at_addition' => 7000,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        (new HandleInvoicePaidJob($invoice))->handle();

        $this->assertCount(3, PluginPayout::all());

        // Plugin A: 3000/10000 * 7000 = 2100
        $payoutA = PluginPayout::where('developer_account_id', $devAccountA->id)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals(2100, $payoutA->gross_amount);
        $splitA = PluginPayout::calculateSplit(2100);
        $this->assertEquals($splitA['platform_fee'], $payoutA->platform_fee);
        $this->assertEquals($splitA['developer_amount'], $payoutA->developer_amount);

        // Plugin B: 5000/10000 * 7000 = 3500
        $payoutB = PluginPayout::where('developer_account_id', $devAccountB->id)->first();
        $this->assertNotNull($payoutB);
        $this->assertEquals(3500, $payoutB->gross_amount);
        $splitB = PluginPayout::calculateSplit(3500);
        $this->assertEquals($splitB['platform_fee'], $payoutB->platform_fee);
        $this->assertEquals($splitB['developer_amount'], $payoutB->developer_amount);

        // Plugin C: remainder = 7000 - 2100 - 3500 = 1400
        $payoutC = PluginPayout::where('developer_account_id', $devAccountC->id)->first();
        $this->assertNotNull($payoutC);
        $this->assertEquals(1400, $payoutC->gross_amount);
        $splitC = PluginPayout::calculateSplit(1400);
        $this->assertEquals($splitC['platform_fee'], $payoutC->platform_fee);
        $this->assertEquals($splitC['developer_amount'], $payoutC->developer_amount);

        // Verify total allocated equals bundle price
        $totalGross = $payoutA->gross_amount + $payoutB->gross_amount + $payoutC->gross_amount;
        $this->assertEquals(7000, $totalGross);
    }

    #[Test]
    public function payout_eligible_date_is_no_less_than_14_days_after_purchase(): void
    {
        [$devAccount, $plugin] = $this->createDeveloperWithPlugin(2999);

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $cart = Cart::factory()->for($buyer)->create();

        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        (new HandleInvoicePaidJob($invoice))->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(PayoutStatus::Pending, $payout->status);

        // The eligible_for_payout_at must be at least 14 days from now
        $this->assertTrue(
            $payout->eligible_for_payout_at->gte(now()->addDays(14)),
            'Payout must not be eligible earlier than 14 days after purchase'
        );
    }

    #[Test]
    public function payout_not_dispatched_before_holding_period_elapses(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        // Create a payout that is 13 days old (within the 14-day minimum)
        PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->addDays(2), // 13 days after a 15-day hold
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('No eligible payouts')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }

    #[Test]
    public function payouts_for_multiple_developers_dispatched_after_holding_period(): void
    {
        Queue::fake();

        [$devAccountA, $pluginA] = $this->createDeveloperWithPlugin(2999);
        [$devAccountB, $pluginB] = $this->createDeveloperWithPlugin(4999);

        $licenseA = PluginLicense::factory()->create(['plugin_id' => $pluginA->id]);
        $licenseB = PluginLicense::factory()->create(['plugin_id' => $pluginB->id]);

        $payoutA = PluginPayout::create([
            'plugin_license_id' => $licenseA->id,
            'developer_account_id' => $devAccountA->id,
            'gross_amount' => 2999,
            'platform_fee' => 900,
            'developer_amount' => 2099,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $payoutB = PluginPayout::create([
            'plugin_license_id' => $licenseB->id,
            'developer_account_id' => $devAccountB->id,
            'gross_amount' => 4999,
            'platform_fee' => 1500,
            'developer_amount' => 3499,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('Dispatched 2 payout transfer job(s)')
            ->assertExitCode(0);

        Queue::assertPushed(ProcessPayoutTransfer::class, 2);
        Queue::assertPushed(ProcessPayoutTransfer::class, fn ($job) => $job->payout->id === $payoutA->id);
        Queue::assertPushed(ProcessPayoutTransfer::class, fn ($job) => $job->payout->id === $payoutB->id);
    }

    #[Test]
    public function mixed_cart_with_individual_plugins_and_bundle_from_different_developers(): void
    {
        // Developer A has a standalone plugin
        [$devAccountA, $pluginA] = $this->createDeveloperWithPlugin(2999);

        // Developers B and C have plugins in a bundle
        [$devAccountB, $pluginB] = $this->createDeveloperWithPlugin(4000);
        [$devAccountC, $pluginC] = $this->createDeveloperWithPlugin(6000);

        // Bundle retail value: 4000 + 6000 = 10000, bundle price: 8000
        $bundle = PluginBundle::factory()->active()->create(['price' => 8000]);
        $bundle->plugins()->attach([
            $pluginB->id => ['sort_order' => 1],
            $pluginC->id => ['sort_order' => 2],
        ]);

        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $cart = Cart::factory()->for($buyer)->create();

        // Individual plugin from Developer A
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $pluginA->id,
            'plugin_price_id' => $pluginA->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        // Bundle containing plugins from Developer B and C
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_bundle_id' => $bundle->id,
            'bundle_price_at_addition' => 8000,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        (new HandleInvoicePaidJob($invoice))->handle();

        // Should create 3 payouts: 1 for individual + 2 for bundle
        $this->assertCount(3, PluginPayout::all());

        // Developer A: standalone plugin at $29.99
        $payoutA = PluginPayout::where('developer_account_id', $devAccountA->id)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals(2999, $payoutA->gross_amount);

        // Developer B: 4000/10000 * 8000 = 3200
        $payoutB = PluginPayout::where('developer_account_id', $devAccountB->id)->first();
        $this->assertNotNull($payoutB);
        $this->assertEquals(3200, $payoutB->gross_amount);

        // Developer C: remainder = 8000 - 3200 = 4800
        $payoutC = PluginPayout::where('developer_account_id', $devAccountC->id)->first();
        $this->assertNotNull($payoutC);
        $this->assertEquals(4800, $payoutC->gross_amount);

        // All payouts should have the holding period set
        PluginPayout::all()->each(function ($payout) {
            $this->assertTrue(
                $payout->eligible_for_payout_at->gte(now()->addDays(14)),
                "Payout {$payout->id} must not be eligible earlier than 14 days after purchase"
            );
        });
    }
}
