<?php

namespace Tests\Feature\Services;

use App\Enums\PayoutStatus;
use App\Enums\StripeConnectStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Account;
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

    #[Test]
    public function create_connect_account_uses_the_recipient_service_agreement_where_full_accounts_cannot_be_paid(): void
    {
        $user = User::factory()->create();
        $accounts = $this->fakeStripeAccounts();

        $developerAccount = app(StripeConnectService::class)->createConnectAccount($user, 'MX', 'MXN');

        $this->assertSame(['transfers' => ['requested' => true]], $accounts->createdWith['capabilities']);
        $this->assertSame(['service_agreement' => 'recipient'], $accounts->createdWith['tos_acceptance']);
        $this->assertSame('MX', $accounts->createdWith['country']);
        $this->assertSame('acct_test_new', $developerAccount->stripe_connect_account_id);
        $this->assertSame('MX', $developerAccount->country);
    }

    #[Test]
    public function create_connect_account_uses_the_full_service_agreement_where_full_accounts_can_be_paid(): void
    {
        $user = User::factory()->create();
        $accounts = $this->fakeStripeAccounts();

        app(StripeConnectService::class)->createConnectAccount($user, 'GB', 'GBP');

        $this->assertSame([
            'card_payments' => ['requested' => true],
            'transfers' => ['requested' => true],
        ], $accounts->createdWith['capabilities']);
        $this->assertArrayNotHasKey('tos_acceptance', $accounts->createdWith);
    }

    #[Test]
    public function replace_connect_account_moves_the_developer_to_a_new_account_that_needs_onboarding(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'stripe_connect_account_id' => 'acct_test_full',
            'country' => 'MX',
            'payout_currency' => 'MXN',
        ]);
        $accounts = $this->fakeStripeAccounts();

        app(StripeConnectService::class)->replaceConnectAccount($developerAccount, 'MX');

        $this->assertSame(['service_agreement' => 'recipient'], $accounts->createdWith['tos_acceptance']);
        $this->assertSame($developerAccount->user->email, $accounts->createdWith['email']);

        $developerAccount->refresh();

        $this->assertSame('acct_test_new', $developerAccount->stripe_connect_account_id);
        $this->assertSame(StripeConnectStatus::Pending, $developerAccount->stripe_connect_status);
        $this->assertFalse($developerAccount->payouts_enabled);
        $this->assertFalse($developerAccount->hasCompletedOnboarding());
        $this->assertFalse($developerAccount->canReceivePayouts());
        $this->assertSame('MXN', $developerAccount->payout_currency);
    }

    #[Test]
    public function refresh_account_status_keeps_the_date_onboarding_was_first_completed(): void
    {
        $onboardedAt = now()->subMonths(3)->startOfSecond();
        $developerAccount = DeveloperAccount::factory()->create(['onboarding_completed_at' => $onboardedAt]);
        $this->fakeStripeAccounts();

        app(StripeConnectService::class)->refreshAccountStatus($developerAccount);

        $this->assertTrue($developerAccount->fresh()->onboarding_completed_at->equalTo($onboardedAt));
    }

    #[Test]
    public function refresh_account_status_marks_a_developer_who_finished_onboarding_as_active(): void
    {
        $developerAccount = DeveloperAccount::factory()->pending()->create();
        $this->fakeStripeAccounts(self::fullAccount());

        app(StripeConnectService::class)->refreshAccountStatus($developerAccount);

        $developerAccount->refresh();

        $this->assertSame(StripeConnectStatus::Active, $developerAccount->stripe_connect_status);
        $this->assertTrue($developerAccount->charges_enabled);
        $this->assertTrue($developerAccount->canReceivePayouts());
        $this->assertTrue($developerAccount->hasCompletedOnboarding());
    }

    #[Test]
    public function refresh_account_status_marks_a_recipient_account_as_active_even_though_it_cannot_take_charges(): void
    {
        $developerAccount = DeveloperAccount::factory()->pending()->create([
            'country' => 'MX',
            'payout_currency' => 'MXN',
        ]);
        $this->fakeStripeAccounts(self::recipientAccount());

        app(StripeConnectService::class)->refreshAccountStatus($developerAccount);

        $developerAccount->refresh();

        $this->assertSame(StripeConnectStatus::Active, $developerAccount->stripe_connect_status);
        $this->assertFalse($developerAccount->charges_enabled);
        $this->assertTrue($developerAccount->payouts_enabled);
        $this->assertTrue($developerAccount->canReceivePayouts());
    }

    /**
     * @param  array<string, mixed>  $account
     */
    #[Test]
    #[DataProvider('accountsThatCannotBePaidYet')]
    public function refresh_account_status_keeps_a_developer_pending_until_stripe_can_pay_them(array $account): void
    {
        $developerAccount = DeveloperAccount::factory()->pending()->create();
        $this->fakeStripeAccounts($account);

        app(StripeConnectService::class)->refreshAccountStatus($developerAccount);

        $developerAccount->refresh();

        $this->assertSame(StripeConnectStatus::Pending, $developerAccount->stripe_connect_status);
        $this->assertFalse($developerAccount->canReceivePayouts());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function accountsThatCannotBePaidYet(): array
    {
        return [
            'recipient account with transfers pending' => [self::recipientAccount(['capabilities' => ['transfers' => 'pending']])],
            'recipient account with transfers inactive' => [self::recipientAccount(['capabilities' => ['transfers' => 'inactive']])],
            'recipient account without payouts enabled' => [self::recipientAccount(['payouts_enabled' => false])],
            'full account with transfers pending' => [self::fullAccount(['capabilities' => ['transfers' => 'pending']])],
            'full account with transfers inactive' => [self::fullAccount(['capabilities' => ['transfers' => 'inactive']])],
            'account without the transfers capability' => [array_merge(self::recipientAccount(), ['capabilities' => []])],
        ];
    }

    /**
     * @param  array<string, mixed>  $account
     */
    #[Test]
    #[DataProvider('disabledAccounts')]
    public function refresh_account_status_marks_a_disabled_account_as_disabled_even_if_it_could_otherwise_be_paid(array $account): void
    {
        $developerAccount = DeveloperAccount::factory()->create();
        $this->fakeStripeAccounts($account);

        app(StripeConnectService::class)->refreshAccountStatus($developerAccount);

        $developerAccount->refresh();

        $this->assertSame(StripeConnectStatus::Disabled, $developerAccount->stripe_connect_status);
        $this->assertTrue($developerAccount->payouts_enabled);
        $this->assertFalse($developerAccount->canReceivePayouts());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function disabledAccounts(): array
    {
        return [
            'recipient account' => [self::recipientAccount(['requirements' => ['disabled_reason' => 'requirements.past_due']])],
            'full account' => [self::fullAccount(['requirements' => ['disabled_reason' => 'under_review']])],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $retrieved  What Stripe returns when the account is retrieved.
     */
    private function fakeStripeAccounts(?array $retrieved = null): object
    {
        $accounts = new class($retrieved ?? self::fullAccount())
        {
            public ?array $createdWith = null;

            public function __construct(private array $retrieved) {}

            public function create(array $params): Account
            {
                $this->createdWith = $params;

                return Account::constructFrom(['id' => 'acct_test_new']);
            }

            public function retrieve(string $id): Account
            {
                return Account::constructFrom(['id' => $id] + $this->retrieved);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->accounts = $accounts;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        return $accounts;
    }

    /**
     * An onboarded Express account on the full service agreement, the way Stripe returns it.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function fullAccount(array $overrides = []): array
    {
        return array_replace_recursive([
            'object' => 'account',
            'type' => 'express',
            'country' => 'GB',
            'charges_enabled' => true,
            'payouts_enabled' => true,
            'details_submitted' => true,
            'capabilities' => [
                'card_payments' => 'active',
                'transfers' => 'active',
            ],
            'requirements' => [
                'currently_due' => [],
                'past_due' => [],
                'disabled_reason' => null,
            ],
            'tos_acceptance' => ['service_agreement' => 'full'],
        ], $overrides);
    }

    /**
     * An onboarded Express account on the recipient service agreement. It only has the
     * transfers capability, so Stripe never enables charges on it.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function recipientAccount(array $overrides = []): array
    {
        return array_replace_recursive([
            'object' => 'account',
            'type' => 'express',
            'country' => 'MX',
            'charges_enabled' => false,
            'payouts_enabled' => true,
            'details_submitted' => true,
            'capabilities' => [
                'transfers' => 'active',
            ],
            'requirements' => [
                'currently_due' => [],
                'past_due' => [],
                'disabled_reason' => null,
            ],
            'tos_acceptance' => ['service_agreement' => 'recipient'],
        ], $overrides);
    }
}
