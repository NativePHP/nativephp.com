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
use Stripe\Collection as StripeCollection;
use Stripe\LineItem;
use Stripe\PaymentIntent;
use Stripe\Refund;
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

    #[Test]
    public function refund_checkout_line_item_refunds_only_what_was_charged_for_that_line(): void
    {
        $refunds = new class
        {
            public ?array $createdWith = null;

            public ?array $createdWithOptions = null;

            public function create(array $params, array $options): Refund
            {
                $this->createdWith = $params;
                $this->createdWithOptions = $options;

                return Refund::constructFrom(['id' => 're_test_123']);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->refunds = $refunds;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        $lineItem = LineItem::constructFrom([
            'id' => 'li_test_plugin',
            'amount_subtotal' => 2900,
            'amount_discount' => 580,
            'amount_tax' => 464,
            'amount_total' => 2784,
        ]);

        $refund = app(StripeConnectService::class)->refundCheckoutLineItem('pi_test_123', $lineItem);

        $this->assertSame('re_test_123', $refund->id);
        $this->assertSame([
            'payment_intent' => 'pi_test_123',
            'amount' => 2784,
        ], $refunds->createdWith);

        // Asking Stripe again for the same line must not create a second refund.
        $this->assertSame(['idempotency_key' => 'refund-li_test_plugin'], $refunds->createdWithOptions);
    }

    #[Test]
    public function checkout_line_items_returns_the_lines_of_the_checkout_session_for_a_payment_intent(): void
    {
        $sessions = $this->fakeStripeCheckoutSessions(
            sessions: [['id' => 'cs_test_123', 'object' => 'checkout.session']],
            lineItems: [
                [
                    'id' => 'li_test_plugin',
                    'object' => 'item',
                    'amount_subtotal' => 2900,
                    'amount_discount' => 580,
                    'amount_tax' => 464,
                    'amount_total' => 2784,
                    'description' => 'acme/camera',
                    'price' => [
                        'id' => 'price_test_plugin',
                        'object' => 'price',
                        'product' => [
                            'id' => 'prod_test_plugin',
                            'object' => 'product',
                            'metadata' => ['plugin_id' => '7'],
                        ],
                    ],
                ],
                [
                    'id' => 'li_test_bundle',
                    'object' => 'item',
                    'amount_subtotal' => 9900,
                    'amount_discount' => 0,
                    'amount_tax' => 0,
                    'amount_total' => 9900,
                    'description' => 'Starter (Bundle)',
                    'price' => [
                        'id' => 'price_test_bundle',
                        'object' => 'price',
                        'product' => [
                            'id' => 'prod_test_bundle',
                            'object' => 'product',
                            'metadata' => ['plugin_bundle_id' => '3'],
                        ],
                    ],
                ],
            ],
        );

        $lineItems = app(StripeConnectService::class)->checkoutLineItems('pi_test_123');

        $this->assertSame(['payment_intent' => 'pi_test_123', 'limit' => 1], $sessions->listedWith);
        $this->assertSame('cs_test_123', $sessions->lineItemsListedFor);
        $this->assertSame(['limit' => 100, 'expand' => ['data.price.product']], $sessions->lineItemsListedWith);

        $this->assertCount(2, $lineItems);
        $this->assertContainsOnlyInstancesOf(LineItem::class, $lineItems);
        $this->assertSame(['li_test_plugin', 'li_test_bundle'], $lineItems->pluck('id')->all());
        $this->assertSame(2784, $lineItems[0]->amount_total);
        $this->assertSame('7', $lineItems[0]->price->product->metadata['plugin_id']);
        $this->assertSame('3', $lineItems[1]->price->product->metadata['plugin_bundle_id']);
    }

    #[Test]
    public function checkout_line_items_is_empty_when_the_payment_intent_has_no_checkout_session(): void
    {
        $sessions = $this->fakeStripeCheckoutSessions(sessions: []);

        $lineItems = app(StripeConnectService::class)->checkoutLineItems('pi_test_no_session');

        $this->assertTrue($lineItems->isEmpty());
        $this->assertSame(['payment_intent' => 'pi_test_no_session', 'limit' => 1], $sessions->listedWith);
        $this->assertNull($sessions->lineItemsListedFor);
    }

    /**
     * @param  array<int, array<string, mixed>>  $sessions  The Checkout sessions Stripe finds for the payment intent.
     * @param  array<int, array<string, mixed>>  $lineItems  The line items of the session.
     */
    private function fakeStripeCheckoutSessions(array $sessions, array $lineItems = []): object
    {
        $checkoutSessions = new class($sessions, $lineItems)
        {
            public ?array $listedWith = null;

            public ?string $lineItemsListedFor = null;

            public ?array $lineItemsListedWith = null;

            public function __construct(private array $sessions, private array $lineItems) {}

            public function all(array $params): StripeCollection
            {
                $this->listedWith = $params;

                return StripeCollection::constructFrom(['object' => 'list', 'data' => $this->sessions]);
            }

            public function allLineItems(string $id, array $params): StripeCollection
            {
                $this->lineItemsListedFor = $id;
                $this->lineItemsListedWith = $params;

                return StripeCollection::constructFrom(['object' => 'list', 'data' => $this->lineItems]);
            }
        };

        $mockCheckout = new \stdClass;
        $mockCheckout->sessions = $checkoutSessions;

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->checkout = $mockCheckout;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);

        return $checkoutSessions;
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
