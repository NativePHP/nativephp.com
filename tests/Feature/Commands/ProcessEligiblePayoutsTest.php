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
use Stripe\Account;
use Stripe\Exception\ApiConnectionException;
use Stripe\StripeClient;
use Tests\TestCase;

class ProcessEligiblePayoutsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_jobs_for_payouts_past_holding_period(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->create();
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

        $developerAccount = DeveloperAccount::factory()->create();
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

        $developerAccount = DeveloperAccount::factory()->create();
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

        $developerAccount = DeveloperAccount::factory()->create();
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

    public function test_heals_held_payout_when_developer_can_now_receive_payouts(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Held,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('Healed 1 held payout(s)')
            ->expectsOutputToContain('Dispatched 1 payout transfer job(s)')
            ->assertExitCode(0);

        $this->assertEquals(PayoutStatus::Pending, $payout->fresh()->status);

        Queue::assertPushed(ProcessPayoutTransfer::class, function ($job) use ($payout) {
            return $job->payout->id === $payout->id;
        });
    }

    public function test_does_not_heal_held_payout_when_developer_still_cannot_receive_payouts(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->pending()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Held,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $accounts = $this->fakeStripeAccounts(canBePaid: false);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('No eligible payouts')
            ->assertExitCode(0);

        $this->assertSame([$developerAccount->stripe_connect_account_id], $accounts->retrieved);
        $this->assertEquals(PayoutStatus::Held, $payout->fresh()->status);

        Queue::assertNothingPushed();
    }

    public function test_checks_stripe_and_releases_held_payouts_once_the_developer_can_be_paid(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->pending()->create();
        $payout = PluginPayout::factory()->create([
            'developer_account_id' => $developerAccount->id,
            'status' => PayoutStatus::Held,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $accounts = $this->fakeStripeAccounts(canBePaid: true);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('Healed 1 held payout(s)')
            ->expectsOutputToContain('Dispatched 1 payout transfer job(s)')
            ->assertExitCode(0);

        $this->assertSame([$developerAccount->stripe_connect_account_id], $accounts->retrieved);
        $this->assertTrue($developerAccount->fresh()->canReceivePayouts());
        $this->assertEquals(PayoutStatus::Pending, $payout->fresh()->status);

        Queue::assertPushed(ProcessPayoutTransfer::class, function ($job) use ($payout) {
            return $job->payout->id === $payout->id;
        });
    }

    public function test_checks_stripe_for_developers_with_pending_payouts_that_are_due(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->pending()->create();
        PluginPayout::factory()->create([
            'developer_account_id' => $developerAccount->id,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $accounts = $this->fakeStripeAccounts(canBePaid: true);

        $this->artisan('payouts:process-eligible')->assertExitCode(0);

        $this->assertSame([$developerAccount->stripe_connect_account_id], $accounts->retrieved);
        $this->assertTrue($developerAccount->fresh()->canReceivePayouts());
    }

    public function test_does_not_check_stripe_for_developers_who_can_be_paid_or_have_nothing_due(): void
    {
        Queue::fake();

        $activeDeveloperAccount = DeveloperAccount::factory()->create();
        PluginPayout::factory()->create([
            'developer_account_id' => $activeDeveloperAccount->id,
            'status' => PayoutStatus::Held,
        ]);

        $developerAccountWithNothingDue = DeveloperAccount::factory()->pending()->create();
        PluginPayout::factory()->create([
            'developer_account_id' => $developerAccountWithNothingDue->id,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->addDays(10),
        ]);

        $accounts = $this->fakeStripeAccounts(canBePaid: true);

        $this->artisan('payouts:process-eligible')->assertExitCode(0);

        $this->assertSame([], $accounts->retrieved);
    }

    public function test_carries_on_with_other_payouts_when_stripe_cannot_be_reached(): void
    {
        Queue::fake();

        $unreachableDeveloperAccount = DeveloperAccount::factory()->pending()->create();
        $heldPayout = PluginPayout::factory()->create([
            'developer_account_id' => $unreachableDeveloperAccount->id,
            'status' => PayoutStatus::Held,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $duePayout = PluginPayout::factory()->create([
            'developer_account_id' => DeveloperAccount::factory()->create()->id,
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ]);

        $this->fakeStripeAccounts(reachable: false);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain("Could not check Stripe for developer account #{$unreachableDeveloperAccount->id}")
            ->expectsOutputToContain('Dispatched 1 payout transfer job(s)')
            ->assertExitCode(0);

        $this->assertEquals(PayoutStatus::Held, $heldPayout->fresh()->status);

        Queue::assertPushed(ProcessPayoutTransfer::class, 1);
        Queue::assertPushed(ProcessPayoutTransfer::class, function ($job) use ($duePayout) {
            return $job->payout->id === $duePayout->id;
        });
    }

    public function test_healed_payout_within_holding_period_is_not_dispatched(): void
    {
        Queue::fake();

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->paid()->create(['user_id' => $developerAccount->user_id]);
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 1000,
            'platform_fee' => 300,
            'developer_amount' => 700,
            'status' => PayoutStatus::Held,
            'eligible_for_payout_at' => now()->addDays(10),
        ]);

        $this->artisan('payouts:process-eligible')
            ->expectsOutputToContain('Healed 1 held payout(s)')
            ->expectsOutputToContain('No eligible payouts')
            ->assertExitCode(0);

        $this->assertEquals(PayoutStatus::Pending, $payout->fresh()->status);

        Queue::assertNothingPushed();
    }

    private function fakeStripeAccounts(bool $canBePaid = false, bool $reachable = true): object
    {
        $accounts = new class($canBePaid, $reachable)
        {
            /** @var list<string> */
            public array $retrieved = [];

            public function __construct(private bool $canBePaid, private bool $reachable) {}

            public function retrieve(string $id): Account
            {
                $this->retrieved[] = $id;

                if (! $this->reachable) {
                    throw new ApiConnectionException('Could not connect to Stripe');
                }

                return Account::constructFrom([
                    'id' => $id,
                    'payouts_enabled' => $this->canBePaid,
                    'charges_enabled' => $this->canBePaid,
                    'details_submitted' => $this->canBePaid,
                    'requirements' => ['disabled_reason' => null],
                ]);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->accounts = $accounts;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        return $accounts;
    }
}
