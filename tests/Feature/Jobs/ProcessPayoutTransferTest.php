<?php

namespace Tests\Feature\Jobs;

use App\Enums\PayoutStatus;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ProcessPayoutTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_calls_process_transfer_on_stripe_connect_service(): void
    {
        $developerAccount = DeveloperAccount::factory()->onboarded()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($payout) {
            $mock->shouldReceive('processTransfer')
                ->once()
                ->with(\Mockery::on(fn ($arg) => $arg->id === $payout->id))
                ->andReturn(true);
        });

        ProcessPayoutTransfer::dispatchSync($payout);
    }
}
