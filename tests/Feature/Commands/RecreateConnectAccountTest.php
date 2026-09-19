<?php

namespace Tests\Feature\Commands;

use App\Enums\PayoutStatus;
use App\Enums\StripeConnectStatus;
use App\Models\DeveloperAccount;
use App\Models\PluginPayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Account;
use Stripe\StripeClient;
use Tests\TestCase;

class RecreateConnectAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_moves_a_developer_in_mexico_to_a_new_recipient_account_and_holds_failed_payouts(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'stripe_connect_account_id' => 'acct_old_full',
            'country' => 'MX',
            'payout_currency' => 'MXN',
        ]);

        $failedPayout = PluginPayout::factory()->failed()->create(['developer_account_id' => $developerAccount->id]);
        $transferredPayout = PluginPayout::factory()->transferred()->create(['developer_account_id' => $developerAccount->id]);
        $otherDevelopersFailedPayout = PluginPayout::factory()->failed()->create();

        $accounts = $this->fakeStripeAccounts(country: 'MX', serviceAgreement: 'full');

        $this->artisan('payouts:recreate-connect-account', ['developerAccount' => $developerAccount->id])
            ->expectsOutputToContain('Replaced acct_old_full with acct_new_recipient')
            ->expectsOutputToContain('Moved 1 failed payout(s) back to held')
            ->assertExitCode(0);

        $this->assertSame('MX', $accounts->createdWith['country']);
        $this->assertSame(['service_agreement' => 'recipient'], $accounts->createdWith['tos_acceptance']);
        $this->assertSame(['transfers' => ['requested' => true]], $accounts->createdWith['capabilities']);

        $developerAccount->refresh();

        $this->assertSame('acct_new_recipient', $developerAccount->stripe_connect_account_id);
        $this->assertSame(StripeConnectStatus::Pending, $developerAccount->stripe_connect_status);
        $this->assertFalse($developerAccount->hasCompletedOnboarding());

        $this->assertSame(PayoutStatus::Held, $failedPayout->fresh()->status);
        $this->assertSame(PayoutStatus::Transferred, $transferredPayout->fresh()->status);
        $this->assertSame(PayoutStatus::Failed, $otherDevelopersFailedPayout->fresh()->status);
    }

    public function test_uses_the_stripe_account_country_when_the_developer_account_has_none(): void
    {
        $developerAccount = DeveloperAccount::factory()->create(['country' => null]);

        $accounts = $this->fakeStripeAccounts(country: 'MX', serviceAgreement: 'full');

        $this->artisan('payouts:recreate-connect-account', ['developerAccount' => $developerAccount->id])
            ->assertExitCode(0);

        $this->assertSame('MX', $accounts->createdWith['country']);
        $this->assertSame('MX', $developerAccount->fresh()->country);
    }

    public function test_leaves_accounts_already_on_the_recipient_agreement_alone(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'stripe_connect_account_id' => 'acct_already_recipient',
            'country' => 'MX',
        ]);
        $failedPayout = PluginPayout::factory()->failed()->create(['developer_account_id' => $developerAccount->id]);

        $accounts = $this->fakeStripeAccounts(country: 'MX', serviceAgreement: 'recipient');

        $this->artisan('payouts:recreate-connect-account', ['developerAccount' => $developerAccount->id])
            ->expectsOutputToContain('already on the recipient service agreement')
            ->assertExitCode(0);

        $this->assertNull($accounts->createdWith);
        $this->assertSame('acct_already_recipient', $developerAccount->fresh()->stripe_connect_account_id);
        $this->assertSame(PayoutStatus::Failed, $failedPayout->fresh()->status);
    }

    public function test_refuses_to_replace_accounts_that_can_be_paid_on_the_full_agreement(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'stripe_connect_account_id' => 'acct_germany',
            'country' => 'DE',
        ]);

        $accounts = $this->fakeStripeAccounts(country: 'DE', serviceAgreement: 'full');

        $this->artisan('payouts:recreate-connect-account', ['developerAccount' => $developerAccount->id])
            ->expectsOutputToContain('can already pay on the full service agreement')
            ->assertExitCode(1);

        $this->assertNull($accounts->createdWith);
        $this->assertSame('acct_germany', $developerAccount->fresh()->stripe_connect_account_id);
        $this->assertTrue($developerAccount->fresh()->hasCompletedOnboarding());
    }

    public function test_fails_when_the_developer_account_does_not_exist(): void
    {
        $this->artisan('payouts:recreate-connect-account', ['developerAccount' => 999])
            ->expectsOutputToContain('Developer account #999 not found')
            ->assertExitCode(1);
    }

    private function fakeStripeAccounts(string $country, string $serviceAgreement): object
    {
        $accounts = new class($country, $serviceAgreement)
        {
            public ?array $createdWith = null;

            public function __construct(private string $country, private string $serviceAgreement) {}

            public function retrieve(string $id): Account
            {
                return Account::constructFrom([
                    'id' => $id,
                    'country' => $this->country,
                    'tos_acceptance' => ['service_agreement' => $this->serviceAgreement],
                ]);
            }

            public function create(array $params): Account
            {
                $this->createdWith = $params;

                return Account::constructFrom(['id' => 'acct_new_recipient']);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->accounts = $accounts;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        return $accounts;
    }
}
