<?php

namespace Tests\Feature\Services;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Transfer;
use Tests\TestCase;

class StripeConnectServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function process_transfer_uses_the_source_charge_currency_not_the_developer_payout_currency(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'payout_currency' => 'EUR',
            'stripe_connect_account_id' => 'acct_test_eur',
        ]);
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'stripe_payment_intent_id' => 'pi_test_usd',
        ]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $capturedTransferParams = null;

        $mockPaymentIntents = new class
        {
            public function retrieve(): PaymentIntent
            {
                return PaymentIntent::constructFrom([
                    'id' => 'pi_test_usd',
                    'currency' => 'usd',
                    'latest_charge' => 'ch_test_usd',
                ]);
            }
        };

        $mockTransfers = new class($capturedTransferParams)
        {
            public function __construct(private &$capturedTransferParams) {}

            public function create(array $params): Transfer
            {
                $this->capturedTransferParams = $params;

                return Transfer::constructFrom(['id' => 'tr_test_123']);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->paymentIntents = $mockPaymentIntents;
        $mockStripeClient->transfers = $mockTransfers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $result = app(StripeConnectService::class)->processTransfer($payout);

        $this->assertTrue($result);
        $this->assertNotNull($capturedTransferParams);
        $this->assertSame('usd', $capturedTransferParams['currency']);
        $this->assertSame('ch_test_usd', $capturedTransferParams['source_transaction']);
        $this->assertSame(700, $capturedTransferParams['amount']);
        $this->assertSame('acct_test_eur', $capturedTransferParams['destination']);

        $this->assertTrue($payout->fresh()->isTransferred());
        $this->assertSame('tr_test_123', $payout->fresh()->stripe_transfer_id);
    }

    #[Test]
    public function process_transfer_falls_back_to_payout_currency_when_charge_lookup_fails(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'payout_currency' => 'EUR',
            'stripe_connect_account_id' => 'acct_test_eur',
        ]);
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'stripe_payment_intent_id' => null,
        ]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $capturedTransferParams = null;

        $mockTransfers = new class($capturedTransferParams)
        {
            public function __construct(private &$capturedTransferParams) {}

            public function create(array $params): Transfer
            {
                $this->capturedTransferParams = $params;

                return Transfer::constructFrom(['id' => 'tr_test_456']);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->transfers = $mockTransfers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $result = app(StripeConnectService::class)->processTransfer($payout);

        $this->assertTrue($result);
        $this->assertNotNull($capturedTransferParams);
        $this->assertSame('eur', $capturedTransferParams['currency']);
        $this->assertArrayNotHasKey('source_transaction', $capturedTransferParams);
    }
}
