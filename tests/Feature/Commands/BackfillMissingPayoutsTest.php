<?php

namespace Tests\Feature\Commands;

use App\Enums\PayoutStatus;
use App\Enums\PluginType;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillMissingPayoutsTest extends TestCase
{
    use RefreshDatabase;

    private function createThirdPartyPlugin(?DeveloperAccount $developerAccount = null): Plugin
    {
        $developerAccount ??= DeveloperAccount::factory()->create();

        return Plugin::factory()->paid()->create([
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
    }

    public function test_creates_pending_payout_using_developer_percentage(): void
    {
        $developerAccount = DeveloperAccount::factory()->create(['payout_percentage' => 80]);
        $plugin = $this->createThirdPartyPlugin($developerAccount);

        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 10000,
            'purchased_at' => now()->subMonth(),
        ]);

        $this->artisan('payouts:backfill')
            ->expectsOutputToContain('Created 1 payout record(s).')
            ->assertExitCode(0);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals($license->id, $payout->plugin_license_id);
        $this->assertEquals($developerAccount->id, $payout->developer_account_id);
        $this->assertEquals(10000, $payout->gross_amount);
        $this->assertEquals(2000, $payout->platform_fee);
        $this->assertEquals(8000, $payout->developer_amount);
        $this->assertEquals(PayoutStatus::Pending, $payout->status);
        $this->assertTrue($payout->eligible_for_payout_at->isPast());
    }

    public function test_creates_held_payout_when_developer_cannot_receive_payouts(): void
    {
        $developerAccount = DeveloperAccount::factory()->pending()->create();
        $plugin = $this->createThirdPartyPlugin($developerAccount);

        PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);

        $this->artisan('payouts:backfill')->assertExitCode(0);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(PayoutStatus::Held, $payout->status);
    }

    public function test_skips_sales_that_already_have_payouts(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        PluginPayout::factory()->create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $plugin->developer_account_id,
        ]);

        $this->artisan('payouts:backfill')
            ->expectsOutputToContain('No sales are missing payout records.')
            ->assertExitCode(0);

        $this->assertEquals(1, PluginPayout::count());
    }

    public function test_skips_official_free_and_grandfathered_sales(): void
    {
        $officialPlugin = Plugin::factory()->paid()->create(['is_official' => true]);
        PluginLicense::factory()->create(['plugin_id' => $officialPlugin->id]);

        $freePlugin = Plugin::factory()->create([
            'is_official' => false,
            'type' => PluginType::Free,
        ]);
        PluginLicense::factory()->create(['plugin_id' => $freePlugin->id]);

        $thirdPartyPlugin = $this->createThirdPartyPlugin();
        PluginLicense::factory()->grandfathered()->create([
            'plugin_id' => $thirdPartyPlugin->id,
            'price_paid' => 0,
        ]);

        $this->artisan('payouts:backfill')
            ->expectsOutputToContain('No sales are missing payout records.')
            ->assertExitCode(0);

        $this->assertEquals(0, PluginPayout::count());
    }

    public function test_dry_run_does_not_create_payouts(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);

        $this->artisan('payouts:backfill --dry-run')
            ->expectsOutputToContain('Found 1 sale(s) missing payout records.')
            ->assertExitCode(0);

        $this->assertEquals(0, PluginPayout::count());
    }
}
