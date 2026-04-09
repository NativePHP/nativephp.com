<?php

namespace Tests\Feature;

use App\Actions\RefundPluginPurchase;
use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
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

            $factory = PluginPayout::factory();
            if ($payoutStatus === 'transferred') {
                $factory = $factory->transferred();
            }

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

    private function mockStripeConnectService(?MockInterface &$mock = null): void
    {
        $this->mock(StripeConnectService::class, function (MockInterface $m) use (&$mock) {
            $m->shouldReceive('refundPaymentIntent')
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

        $this->mockStripeConnectService();

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
        $this->mockStripeConnectService($mock);
        $mock->shouldNotHaveReceived('reverseTransfer');

        app(RefundPluginPurchase::class)->handle($license, User::factory()->create());

        $payout->refresh();
        $this->assertEquals(PayoutStatus::Cancelled, $payout->status);
    }

    #[Test]
    public function transferred_payout_gets_reversed_and_cancelled_on_refund(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout(
            ['purchased_at' => now()->subDays(3)],
            'transferred',
        );

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($payout) {
            $mock->shouldReceive('refundPaymentIntent')
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

        $this->mock(StripeConnectService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refundPaymentIntent')
                ->once()
                ->andReturn($this->makeStripeRefund('re_bundle_refund'));
            $mock->shouldReceive('reverseTransfer')->never();
        });

        app(RefundPluginPurchase::class)->handle($licenses->first(), User::factory()->create());

        foreach ($licenses as $license) {
            $license->refresh();
            $this->assertNotNull($license->refunded_at);
            $this->assertEquals('re_bundle_refund', $license->stripe_refund_id);

            $this->assertEquals(PayoutStatus::Cancelled, $license->payout->status);
        }
    }

    #[Test]
    public function stripe_failure_leaves_everything_unchanged(): void
    {
        [$license, $payout] = $this->createLicenseWithPayout([
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refundPaymentIntent')
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
    public function refunded_license_is_excluded_from_is_active_and_active_scope(): void
    {
        $license = PluginLicense::factory()->refunded()->create();

        $this->assertFalse($license->isActive());

        $activeCount = PluginLicense::query()->active()->where('id', $license->id)->count();
        $this->assertEquals(0, $activeCount);
    }
}
