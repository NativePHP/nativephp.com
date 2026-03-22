<?php

namespace Tests\Feature\Commands;

use App\Enums\PayoutStatus;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessEligiblePayoutsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_jobs_for_payouts_past_holding_period(): void
    {
        Queue::fake();

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

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('Dispatched 1 payout transfer job(s)')
            ->assertExitCode(0);

        Queue::assertPushed(ProcessPayoutTransfer::class, function ($job) use ($payout) {
            return $job->payout->id === $payout->id;
        });
    }

    public function test_skips_payouts_still_within_holding_period(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->onboarded()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->addDays(10),
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('No eligible payouts')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }

    public function test_skips_non_pending_payouts(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->onboarded()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Transferred,
            'eligible_for_payout_at' => now()->subDay(),
            'transferred_at' => now()->subDay(),
            'stripe_transfer_id' => 'tr_test',
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('No eligible payouts')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }

    public function test_dispatches_only_eligible_payouts_among_mixed(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->onboarded()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);

        // Eligible payout (past holding period)
        $eligibleLicense = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);
        $eligiblePayout = PluginPayout::create([
            'plugin_license_id' => $eligibleLicense->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDays(2),
        ]);

        // Not yet eligible payout (still in holding period)
        $futureLicense = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);
        PluginPayout::create([
            'plugin_license_id' => $futureLicense->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 2000,
            'platform_fee' => 600,
            'developer_amount' => 1400,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->addDays(10),
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('Dispatched 1 payout transfer job(s)')
            ->assertExitCode(0);

        Queue::assertPushed(ProcessPayoutTransfer::class, 1);
        Queue::assertPushed(ProcessPayoutTransfer::class, function ($job) use ($eligiblePayout) {
            return $job->payout->id === $eligiblePayout->id;
        });
    }

    public function test_returns_success_when_no_payouts_exist(): void
    {
        Queue::fake();

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('No eligible payouts')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }
}
