<?php

namespace Tests\Feature;

use App\Actions\RefundPluginPurchase;
use App\Enums\PayoutStatus;
use App\Jobs\HandleInvoicePaidJob;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\User;
use App\Services\CartService;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\Matcher\Closure as MockeryClosure;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Invoice;
use Stripe\LineItem;
use Stripe\Refund;
use Stripe\TransferReversal;
use Tests\TestCase;

class PluginPurchaseRefundTest extends TestCase
{
    use RefreshDatabase;

    private function createLicenseWithPayout(array $licenseAttributes = [], ?string $payoutStatus = 'pending'): array
    {
        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(array_merge(
            ['plugin_id' => $plugin->id],
            $licenseAttributes,
        ));

        $payout = null;
        if ($payoutStatus) {
            $payoutData = [
                'plugin_license_id' => $license->id,
                'developer_account_id' => $developerAccount->id,
            ];

            $factory = match ($payoutStatus) {
                'transferred' => PluginPayout::factory()->transferred(),
                'failed' => PluginPayout::factory()->failed(),
                'held' => PluginPayout::factory()->state(['status' => PayoutStatus::Held]),
                default => PluginPayout::factory(),
            };

            $payout = $factory->create($payoutData);
        }

        return [$license, $payout, $developerAccount];
    }

    private function makeStripeRefund(string $id = 're_test_refund_123'): Refund
    {
        return Refund::constructFrom(['id' => $id]);
    }

    private function makeStripeTransferReversal(string $id = 'trr_test_reversal_123'): TransferReversal
    {
        return TransferReversal::constructFrom(['id' => $id]);
    }

    /**
     * A line of a Stripe checkout, the way Stripe returns it with the product expanded.
     * It is charged at $29.00 with no discount or tax unless $amounts says otherwise.
     *
     * @param  array<string, string>  $metadata  The product's metadata. Checkouts from before lines were tagged have none.
     * @param  array<string, int>  $amounts
     */
    private function makeStripeLineItem(string $description, array $metadata = [], array $amounts = []): LineItem
    {
        return LineItem::constructFrom(array_merge([
            'amount_subtotal' => 2900,
            'amount_discount' => 0,
            'amount_tax' => 0,
            'amount_total' => 2900,
            'description' => $description,
            'price' => ['product' => ['id' => 'prod_test_123', 'metadata' => $metadata]],
        ], $amounts));
    }

    /**
     * @param  array<string, int>  $amounts
     */
    private function makePluginLineItem(Plugin $plugin, array $amounts = []): LineItem
    {
        return $this->makeStripeLineItem($plugin->name, ['plugin_id' => (string) $plugin->id], $amounts);
    }

    /**
     * @param  array<string, int>  $amounts
     */
    private function makeBundleLineItem(PluginBundle $bundle, array $amounts = []): LineItem
    {
        return $this->makeStripeLineItem($bundle->name.' (Bundle)', ['plugin_bundle_id' => (string) $bundle->id], $amounts);
    }

    /**
     * Matches the checkout line handed to Stripe for refunding, by what was charged for it.
     */
    private function lineItemCharged(int $amount): MockeryClosure
    {
        return Mockery::on(fn (LineItem $lineItem): bool => $lineItem->amount_total === $amount);
    }

    /**
     * @param  array<int, LineItem>  $lineItems  The lines of the Stripe checkout the license was bought in.
     */
    private function mockStripeConnectService(array $lineItems = [], ?MockInterface &$mock = null): void
    {
        $this->mock(StripeConnectService::class, function (MockInterface $m) use ($lineItems, &$mock) {
            $m->shouldReceive('checkoutLineItems')
                ->andReturn(collect($lineItems));
            $m->shouldReceive('refundCheckoutLineItem')
                ->andReturn($this->makeStripeRefund());
            $m->shouldReceive('reverseTransfer')
                ->andReturn($this->makeStripeTransferReversal());
            $mock = $m;
        });
    }

    #[Test]
    public function refund_within_14_days_succeeds(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(5),
        ]);

        $this->mockStripeConnectService([$this->makePluginLineItem($license->plugin)]);

        $admin = User::factory()->create();
        app(RefundPluginPurchase::class)->handle($license, $admin);

        $license->refresh();
        $this->assertNotNull($license->refunded_at);
        $this->assertEquals('re_test_refund_123', $license->stripe_refund_id);
        $this->assertEquals($admin->id, $license->refunded_by);

        $payout->refresh();
        $this->assertTrue($payout->isCancelled());
    }

    #[Test]
    public function refund_after_14_days_is_blocked(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(15),
        ]);

        $this->mockStripeConnectService();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not eligible for a refund');

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());
    }

    #[Test]
    public function refund_of_comped_license_is_blocked(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'is_grandfathered' => true,
            'price_paid' => 5000,
        ]);

        $this->mockStripeConnectService();

        $this->expectException(\RuntimeException::class);

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());
    }

    #[Test]
    public function refund_of_zero_price_license_is_blocked(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'price_paid' => 0,
        ]);

        $this->mockStripeConnectService();

        $this->expectException(\RuntimeException::class);

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());
    }

    #[Test]
    public function refund_of_already_refunded_license_is_blocked(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'refunded_at' => now(),
            'stripe_refund_id' => 're_existing',
        ]);

        $this->mockStripeConnectService();

        $this->expectException(\RuntimeException::class);

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());
    }

    #[Test]
    public function refund_of_license_without_stripe_payment_intent_is_blocked(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'stripe_payment_intent_id' => null,
        ]);

        $this->mockStripeConnectService();

        $this->expectException(\RuntimeException::class);

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());
    }

    #[Test]
    public function pending_payout_gets_cancelled_on_refund(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout(
            ['purchased_at' => now()->subDays(3)],
            'pending',
        );

        $mock = null;
        $this->mockStripeConnectService([$this->makePluginLineItem($license->plugin)], $mock);
        $mock->shouldNotHaveReceived('reverseTransfer');

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Cancelled, $payout->status);
    }

    #[Test]
    public function held_payout_gets_cancelled_on_refund(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout(
            ['purchased_at' => now()->subDays(3)],
            'held',
        );

        $this->mockStripeConnectService([$this->makePluginLineItem($license->plugin)]);

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $this->assertEquals(PayoutStatus::Cancelled, $payout->refresh()->status);
    }

    #[Test]
    public function failed_payout_gets_cancelled_on_refund_so_it_cannot_be_retried(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout(
            ['purchased_at' => now()->subDays(3)],
            'failed',
        );

        $this->mockStripeConnectService([$this->makePluginLineItem($license->plugin)]);

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Cancelled, $payout->status);
        $this->assertFalse($payout->isFailed());
    }

    #[Test]
    public function transferred_payout_gets_reversed_and_cancelled_on_refund(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout(
            ['purchased_at' => now()->subDays(3)],
            'transferred',
        );

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license, $payout) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([$this->makePluginLineItem($license->plugin)]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->andReturn($this->makeStripeRefund());
            $mock->shouldReceive('reverseTransfer')
                ->once()
                ->with($payout->stripe_transfer_id)
                ->andReturn($this->makeStripeTransferReversal());
        });

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Cancelled, $payout->status);
    }

    #[Test]
    public function refunding_one_plugin_from_a_multi_item_checkout_only_refunds_that_line(): void
    {
        $buyer = User::factory()->create();
        $paymentIntentId = 'pi_multi_item_test_123';

        [$license, $payout] = $this->createLicenseWithPayout([
            'user_id' => $buyer->id,
            'stripe_payment_intent_id' => $paymentIntentId,
            'purchased_at' => now()->subDays(3),
            'price_paid' => 2900,
        ]);
        [$otherLicense, $otherPayout] = $this->createLicenseWithPayout([
            'user_id' => $buyer->id,
            'stripe_payment_intent_id' => $paymentIntentId,
            'purchased_at' => now()->subDays(3),
            'price_paid' => 4900,
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license, $otherLicense, $paymentIntentId) {
            $mock->shouldReceive('checkoutLineItems')
                ->once()
                ->with($paymentIntentId)
                ->andReturn(collect([
                    $this->makePluginLineItem($otherLicense->plugin, [
                        'amount_subtotal' => 4900,
                        'amount_discount' => 980,
                        'amount_tax' => 784,
                        'amount_total' => 4704,
                    ]),
                    $this->makePluginLineItem($license->plugin, [
                        'amount_subtotal' => 2900,
                        'amount_discount' => 580,
                        'amount_tax' => 464,
                        'amount_total' => 2784,
                    ]),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($paymentIntentId, $this->lineItemCharged(2784))
                ->andReturn($this->makeStripeRefund());
            $mock->shouldReceive('reverseTransfer')->never();
        });

        $amount = app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $this->assertSame(2784, $amount);

        $license->refresh();
        $this->assertNotNull($license->refunded_at);
        $this->assertEquals('re_test_refund_123', $license->stripe_refund_id);
        $this->assertEquals(PayoutStatus::Cancelled, $payout->refresh()->status);

        $otherLicense->refresh();
        $this->assertNull($otherLicense->refunded_at);
        $this->assertNull($otherLicense->stripe_refund_id);
        $this->assertEquals(PayoutStatus::Pending, $otherPayout->refresh()->status);
    }

    #[Test]
    public function bundle_refund_processes_all_sibling_licenses(): void
    {
        $bundle = PluginBundle::factory()->create();
        $developerAccount = DeveloperAccount::factory()->create();
        $paymentIntentId = 'pi_bundle_test_123';

        $licenses = collect();
        for ($i = 0; $i < 3; $i++) {
            $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
            $license = PluginLicense::factory()->create([
                'plugin_id' => $plugin->id,
                'plugin_bundle_id' => $bundle->id,
                'stripe_payment_intent_id' => $paymentIntentId,
                'purchased_at' => now()->subDays(3),
                'price_paid' => 3000,
            ]);

            PluginPayout::factory()->create([
                'plugin_license_id' => $license->id,
                'developer_account_id' => $developerAccount->id,
            ]);

            $licenses->push($license);
        }

        $boughtAlongside = Plugin::factory()->paid()->create();

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($bundle, $boughtAlongside, $paymentIntentId) {
            $mock->shouldReceive('checkoutLineItems')
                ->once()
                ->with($paymentIntentId)
                ->andReturn(collect([
                    $this->makePluginLineItem($boughtAlongside),
                    $this->makeBundleLineItem($bundle, [
                        'amount_subtotal' => 9000,
                        'amount_discount' => 1800,
                        'amount_tax' => 1440,
                        'amount_total' => 8640,
                    ]),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($paymentIntentId, $this->lineItemCharged(8640))
                ->andReturn($this->makeStripeRefund('re_bundle_refund'));
            $mock->shouldReceive('reverseTransfer')->never();
        });

        $amount = app(RefundPluginPurchase::class)->handle($licenses->first(), User::factory()->create());

        $this->assertSame(8640, $amount);

        foreach ($licenses as $license) {
            $license->refresh();
            $this->assertNotNull($license->refunded_at);
            $this->assertEquals('re_bundle_refund', $license->stripe_refund_id);

            $this->assertEquals(PayoutStatus::Cancelled, $license->payout->status);
        }
    }

    #[Test]
    public function plugin_line_is_matched_by_id_even_after_the_plugin_is_renamed(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $lineItem = $this->makePluginLineItem($license->plugin);
        $license->plugin->update(['name' => 'acme/renamed-since-purchase']);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license, $lineItem) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([$lineItem]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($license->stripe_payment_intent_id, $this->lineItemCharged(2900))
                ->andReturn($this->makeStripeRefund());
        });

        $amount = app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $this->assertSame(2900, $amount);
        $this->assertTrue($license->fresh()->isRefunded());
    }

    #[Test]
    public function bundle_line_is_matched_by_id_even_after_the_bundle_is_renamed(): void
    {
        $bundle = PluginBundle::factory()->create();
        [$license] = $this->createLicenseWithPayout([
            'plugin_bundle_id' => $bundle->id,
            'purchased_at' => now()->subDays(3),
        ]);

        $lineItem = $this->makeBundleLineItem($bundle);
        $bundle->update(['name' => 'Renamed Since Purchase']);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license, $lineItem) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([$lineItem]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($license->stripe_payment_intent_id, $this->lineItemCharged(2900))
                ->andReturn($this->makeStripeRefund());
        });

        $amount = app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $this->assertSame(2900, $amount);
        $this->assertTrue($license->fresh()->isRefunded());
    }

    #[Test]
    public function plugin_line_without_metadata_is_matched_by_name(): void
    {
        [$license] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([
                    $this->makeStripeLineItem('The NativePHP Masterclass', amounts: [
                        'amount_subtotal' => 29900,
                        'amount_total' => 29900,
                    ]),
                    $this->makeStripeLineItem($license->plugin->name),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($license->stripe_payment_intent_id, $this->lineItemCharged(2900))
                ->andReturn($this->makeStripeRefund());
        });

        $amount = app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $this->assertSame(2900, $amount);
        $this->assertTrue($license->fresh()->isRefunded());
    }

    #[Test]
    public function bundle_line_without_metadata_is_matched_by_name_with_the_bundle_suffix(): void
    {
        $bundle = PluginBundle::factory()->create();
        [$license] = $this->createLicenseWithPayout([
            'plugin_bundle_id' => $bundle->id,
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($bundle, $license) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([
                    $this->makeStripeLineItem($license->plugin->name),
                    $this->makeStripeLineItem($bundle->name.' (Bundle)', amounts: [
                        'amount_subtotal' => 9900,
                        'amount_total' => 9900,
                    ]),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($license->stripe_payment_intent_id, $this->lineItemCharged(9900))
                ->andReturn($this->makeStripeRefund());
        });

        $amount = app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $this->assertSame(9900, $amount);
        $this->assertTrue($license->fresh()->isRefunded());
    }

    #[Test]
    public function refund_is_blocked_when_the_checkout_only_has_a_line_for_something_else(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([
                    $this->makeStripeLineItem('The NativePHP Masterclass', amounts: [
                        'amount_subtotal' => 29900,
                        'amount_total' => 29900,
                    ]),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')->never();
            $mock->shouldReceive('reverseTransfer')->never();
        });

        $this->assertThrows(
            fn () => app(RefundPluginPurchase::class)->handle($license, User::factory()->create()),
            \RuntimeException::class,
            'Could not find this license on its Stripe checkout, so there is no amount to refund.',
        );

        $license->refresh();
        $this->assertNull($license->refunded_at);
        $this->assertNull($license->stripe_refund_id);
        $this->assertEquals(PayoutStatus::Pending, $payout->refresh()->status);
    }

    #[Test]
    public function refund_is_blocked_when_nothing_was_charged_for_the_line(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([
                    $this->makePluginLineItem($license->plugin, [
                        'amount_discount' => 2900,
                        'amount_total' => 0,
                    ]),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')->never();
            $mock->shouldReceive('reverseTransfer')->never();
        });

        $this->assertThrows(
            fn () => app(RefundPluginPurchase::class)->handle($license, User::factory()->create()),
            \RuntimeException::class,
            'Nothing was charged for this license, so there is nothing to refund.',
        );

        $license->refresh();
        $this->assertNull($license->refunded_at);
        $this->assertNull($license->stripe_refund_id);
        $this->assertEquals(PayoutStatus::Pending, $payout->refresh()->status);
    }

    #[Test]
    public function refund_is_blocked_when_the_checkout_has_no_line_items(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect());
            $mock->shouldReceive('refundCheckoutLineItem')->never();
            $mock->shouldReceive('reverseTransfer')->never();
        });

        $this->assertThrows(
            fn () => app(RefundPluginPurchase::class)->handle($license, User::factory()->create()),
            \RuntimeException::class,
            'Could not find this license on its Stripe checkout, so there is no amount to refund.',
        );

        $license->refresh();
        $this->assertNull($license->refunded_at);
        $this->assertNull($license->stripe_refund_id);
        $this->assertEquals(PayoutStatus::Pending, $payout->refresh()->status);
    }

    #[Test]
    public function stripe_failure_leaves_everything_unchanged(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license) {
            $mock->shouldReceive('checkoutLineItems')
                ->andReturn(collect([$this->makePluginLineItem($license->plugin)]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->andThrow(new \Exception('Stripe API error'));
        });

        try {
            app(RefundPluginPurchase::class)->handle($license, User::factory()->create());
        } catch (\Exception) {
            // Expected
        }

        $license->refresh();
        $this->assertNull($license->refunded_at);
        $this->assertNull($license->stripe_refund_id);

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Pending, $payout->status);
    }

    #[Test]
    public function buyer_loses_access_when_refunded_and_regains_it_by_buying_again(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_'.uniqid()]);
        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/rebuyable-plugin',
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2900)->create(['plugin_id' => $plugin->id]);

        $firstPurchase = PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'plugin_id' => $plugin->id,
            'price_paid' => 2900,
            'purchased_at' => now()->subDays(3),
        ]);
        $this->assertTrue($buyer->hasPluginAccess($plugin));

        $this->mockStripeConnectService([$this->makePluginLineItem($plugin)]);
        app(RefundPluginPurchase::class)->handle($firstPurchase, User::factory()->create());

        $this->assertFalse($buyer->hasPluginAccess($plugin));

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2900,
        ]);

        $removed = app(CartService::class)->removeAlreadyOwned($cart->fresh('items'), $buyer);
        $this->assertSame([], $removed, 'A refunded plugin should not count as already owned');

        (new HandleInvoicePaidJob(Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $buyer->stripe_id,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'metadata' => ['cart_id' => $cart->id],
            'lines' => [],
        ])))->handle();

        $this->assertTrue($buyer->hasPluginAccess($plugin));
        $this->assertSame(2, $buyer->pluginLicenses()->forPlugin($plugin)->count());
        $this->assertTrue($firstPurchase->fresh()->isRefunded());

        $secondPurchase = $buyer->pluginLicenses()->forPlugin($plugin)->notRefunded()->sole();
        $this->assertTrue($secondPurchase->isRefundable());
        $this->assertTrue($secondPurchase->payout->isPending());
    }

    #[Test]
    public function refunded_license_is_excluded_from_is_active_and_active_scope(): void
    {
        $license = PluginLicense::factory()->refunded()->create();

        $this->assertFalse($license->isActive());

        $activeCount = PluginLicense::query()->active()->where('id', $license->id)->count();
        $this->assertEquals(0, $activeCount);
    }
}
