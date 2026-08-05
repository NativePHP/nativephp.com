<?php

namespace Tests\Feature;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\StripeClient;
use Tests\TestCase;

class StripeConnectPayoutFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_transfer_records_attempt_and_failure_reason(): void
    {
        $errorMessage = "The currency of source_transaction's balance transaction (usd) must be the same as the transfer currency (eur)";

        $mockTransfers = new class($errorMessage)
        {
            public function __construct(private string $errorMessage) {}

            public function create(array $params): never
            {
                throw new \Exception($this->errorMessage);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->transfers = $mockTransfers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $developerAccount = DeveloperAccount::factory()->create(['payout_currency' => 'EUR']);
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'stripe_payment_intent_id' => null,
        ]);

        $payout = PluginPayout::factory()->create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'status' => PayoutStatus::Pending,
        ]);

        $service = app(StripeConnectService::class);
        $result = $service->processTransfer($payout);

        $this->assertFalse($result);

        $payout->refresh();

        $this->assertTrue($payout->isFailed());
        $this->assertStringContainsString('currency of source_transaction', $payout->failure_reason);
        $this->assertEquals(1, $payout->attempt_count);
        $this->assertNotNull($payout->last_attempted_at);

        $this->assertCount(1, $payout->attempts()->get());
        $attempt = $payout->attempts()->first();
        $this->assertFalse($attempt->succeeded);
        $this->assertStringContainsString('currency of source_transaction', $attempt->error_message);
    }

    public function test_successful_transfer_records_succeeded_attempt(): void
    {
        $mockTransfers = new class
        {
            public function create(array $params): object
            {
                return (object) ['id' => 'tr_test_123'];
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->transfers = $mockTransfers;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'stripe_payment_intent_id' => null,
        ]);

        $payout = PluginPayout::factory()->create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'status' => PayoutStatus::Pending,
        ]);

        $service = app(StripeConnectService::class);
        $result = $service->processTransfer($payout);

        $this->assertTrue($result);

        $payout->refresh();

        $this->assertTrue($payout->isTransferred());
        $this->assertEquals('tr_test_123', $payout->stripe_transfer_id);
        $this->assertEquals(1, $payout->attempt_count);
        $this->assertNull($payout->failure_reason);

        $attempt = $payout->attempts()->first();
        $this->assertTrue($attempt->succeeded);
        $this->assertEquals('tr_test_123', $attempt->stripe_transfer_id);
    }
}
