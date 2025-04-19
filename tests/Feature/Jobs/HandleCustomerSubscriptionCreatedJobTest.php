<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CreateAnystackLicenseJob;
use App\Jobs\StripeWebhooks\HandleCustomerSubscriptionCreatedJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Customer;
use Stripe\StripeClient;
use Tests\TestCase;

class HandleCustomerSubscriptionCreatedJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration for Stripe prices
        Config::set('subscriptions.plans', [
            'test' => [
                'name' => 'Test Plan',
                'stripe_price_id' => 'price_1RFFxGAyFo6rlwXqt5B1eNEF',
                'anystack_product_id' => 'test-product-id',
                'anystack_policy_id' => 'test-policy-id',
            ],
        ]);
    }

    /** @test */
    public function it_dispatches_the_create_anystack_license_job_with_correct_data()
    {
        Bus::fake();

        // Mock the Stripe client and customer response
        $mockCustomer = Customer::constructFrom([
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        $this->mockStripeClient($mockCustomer);

        // Create a webhook call with the test payload
        $webhookCall = WebhookCall::make()->forceFill([
            'name' => 'stripe',
            'url' => 'https://example.com/webhook',
            'headers' => ['Stripe-Signature' => 'test'],
            'payload' => $this->getTestWebhookPayload(),
        ]);

        // Run the job
        $job = new HandleCustomerSubscriptionCreatedJob($webhookCall);
        $job->handle();

        // Assert that the CreateAnystackLicenseJob was dispatched with the correct parameters
        Bus::assertDispatched(CreateAnystackLicenseJob::class, function ($job) {
            return $job->email === 'test@example.com' &&
                   $job->productId === 'test-product-id' &&
                   $job->policyId === 'test-policy-id' &&
                   $job->firstName === 'John' &&
                   $job->lastName === 'Doe';
        });
    }

    /**
     * @dataProvider customerNameProvider
     *
     * @test
     */
    public function it_extracts_customer_name_parts_correctly($fullName, $expectedFirstName, $expectedLastName)
    {
        Bus::fake();

        $mockCustomer = Customer::constructFrom([
            'email' => 'test@example.com',
            'name' => $fullName,
        ]);

        $this->mockStripeClient($mockCustomer);

        $webhookCall = WebhookCall::make()->forceFill([
            'name' => 'stripe',
            'url' => 'https://example.com/webhook',
            'headers' => ['Stripe-Signature' => 'test'],
            'payload' => $this->getTestWebhookPayload(),
        ]);

        $job = new HandleCustomerSubscriptionCreatedJob($webhookCall);
        $job->handle();

        Bus::assertDispatched(CreateAnystackLicenseJob::class, function ($job) use ($expectedFirstName, $expectedLastName) {
            return $job->firstName === $expectedFirstName &&
                   $job->lastName === $expectedLastName;
        });
    }

    /**
     * Data provider for customer name tests
     */
    public function customerNameProvider()
    {
        return [
            'Full name' => ['John Doe', 'John', 'Doe'],
            'First name only' => ['Jane', 'Jane', null],
            'Empty string' => ['', null, null],
            'Null value' => [null, null, null],
        ];
    }

    /** @test */
    public function it_fails_when_customer_has_no_email()
    {
        Bus::fake();

        $mockCustomer = Customer::constructFrom([
            'email' => null,
            'name' => 'John Doe',
        ]);

        $this->mockStripeClient($mockCustomer);

        $webhookCall = WebhookCall::make()->forceFill([
            'name' => 'stripe',
            'url' => 'https://example.com/webhook',
            'headers' => ['Stripe-Signature' => 'test'],
            'payload' => $this->getTestWebhookPayload(),
        ]);

        $job = new HandleCustomerSubscriptionCreatedJob($webhookCall);
        $job->handle();

        Bus::assertNotDispatched(CreateAnystackLicenseJob::class);
    }

    protected function getTestWebhookPayload(): array
    {
        return [
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_1RFKQDAyFo6rlwXq6Wuu642C',
                    'object' => 'subscription',
                    'application' => null,
                    'application_fee_percent' => null,
                    'automatic_tax' => [
                        'disabled_reason' => null,
                        'enabled' => false,
                        'liability' => null,
                    ],
                    'billing_cycle_anchor' => 1745003875,
                    'billing_cycle_anchor_config' => null,
                    'billing_thresholds' => null,
                    'cancel_at' => null,
                    'cancel_at_period_end' => false,
                    'canceled_at' => null,
                    'cancellation_details' => [
                        'comment' => null,
                        'feedback' => null,
                        'reason' => null,
                    ],
                    'collection_method' => 'charge_automatically',
                    'created' => 1745003875,
                    'currency' => 'usd',
                    'current_period_end' => 1776539875,
                    'current_period_start' => 1745003875,
                    'customer' => 'cus_S9dhoV2rJK2Auy',
                    'days_until_due' => null,
                    'default_payment_method' => 'pm_1RFKQBAyFo6rlwXq0zprYwdm',
                    'default_source' => null,
                    'default_tax_rates' => [],
                    'description' => null,
                    'discount' => null,
                    'discounts' => [],
                    'ended_at' => null,
                    'invoice_settings' => [
                        'account_tax_ids' => null,
                        'issuer' => [
                            'type' => 'self',
                        ],
                    ],
                    'items' => [
                        'object' => 'list',
                        'data' => [
                            [
                                'id' => 'si_S9dhjbP3rnMPYq',
                                'object' => 'subscription_item',
                                'billing_thresholds' => null,
                                'created' => 1745003876,
                                'current_period_end' => 1776539875,
                                'current_period_start' => 1745003875,
                                'discounts' => [],
                                'metadata' => [],
                                'plan' => [
                                    'id' => 'price_1RFFxGAyFo6rlwXqt5B1eNEF',
                                    'object' => 'plan',
                                    'active' => true,
                                    'aggregate_usage' => null,
                                    'amount' => 25000,
                                    'amount_decimal' => '25000',
                                    'billing_scheme' => 'per_unit',
                                    'created' => 1744986706,
                                    'currency' => 'usd',
                                    'interval' => 'year',
                                    'interval_count' => 1,
                                    'livemode' => false,
                                    'metadata' => [],
                                    'meter' => null,
                                    'nickname' => null,
                                    'product' => 'prod_S9Z5CgycbP7P4y',
                                    'tiers_mode' => null,
                                    'transform_usage' => null,
                                    'trial_period_days' => null,
                                    'usage_type' => 'licensed',
                                ],
                                'price' => [
                                    'id' => 'price_1RFFxGAyFo6rlwXqt5B1eNEF',
                                    'object' => 'price',
                                    'active' => true,
                                    'billing_scheme' => 'per_unit',
                                    'created' => 1744986706,
                                    'currency' => 'usd',
                                    'custom_unit_amount' => null,
                                    'livemode' => false,
                                    'lookup_key' => null,
                                    'metadata' => [],
                                    'nickname' => null,
                                    'product' => 'prod_S9Z5CgycbP7P4y',
                                    'recurring' => [
                                        'aggregate_usage' => null,
                                        'interval' => 'year',
                                        'interval_count' => 1,
                                        'meter' => null,
                                        'trial_period_days' => null,
                                        'usage_type' => 'licensed',
                                    ],
                                    'tax_behavior' => 'unspecified',
                                    'tiers_mode' => null,
                                    'transform_quantity' => null,
                                    'type' => 'recurring',
                                    'unit_amount' => 25000,
                                    'unit_amount_decimal' => '25000',
                                ],
                                'quantity' => 1,
                                'subscription' => 'sub_1RFKQDAyFo6rlwXq6Wuu642C',
                                'tax_rates' => [],
                            ],
                        ],
                        'has_more' => false,
                        'total_count' => 1,
                        'url' => '/v1/subscription_items?subscription=sub_1RFKQDAyFo6rlwXq6Wuu642C',
                    ],
                    'latest_invoice' => 'in_1RFKQEAyFo6rlwXqBa5IhGhF',
                    'livemode' => false,
                    'metadata' => [],
                    'next_pending_invoice_item_invoice' => null,
                    'on_behalf_of' => null,
                    'pause_collection' => null,
                    'payment_settings' => [
                        'payment_method_options' => [
                            'acss_debit' => null,
                            'bancontact' => null,
                            'card' => [
                                'network' => null,
                                'request_three_d_secure' => 'automatic',
                            ],
                            'customer_balance' => null,
                            'konbini' => null,
                            'sepa_debit' => null,
                            'us_bank_account' => null,
                        ],
                        'payment_method_types' => null,
                        'save_default_payment_method' => 'off',
                    ],
                    'pending_invoice_item_interval' => null,
                    'pending_setup_intent' => null,
                    'pending_update' => null,
                    'plan' => [
                        'id' => 'price_1RFFxGAyFo6rlwXqt5B1eNEF',
                        'object' => 'plan',
                        'active' => true,
                        'aggregate_usage' => null,
                        'amount' => 25000,
                        'amount_decimal' => '25000',
                        'billing_scheme' => 'per_unit',
                        'created' => 1744986706,
                        'currency' => 'usd',
                        'interval' => 'year',
                        'interval_count' => 1,
                        'livemode' => false,
                        'metadata' => [],
                        'meter' => null,
                        'nickname' => null,
                        'product' => 'prod_S9Z5CgycbP7P4y',
                        'tiers_mode' => null,
                        'transform_usage' => null,
                        'trial_period_days' => null,
                        'usage_type' => 'licensed',
                    ],
                    'quantity' => 1,
                    'schedule' => null,
                    'start_date' => 1745003875,
                    'status' => 'active',
                    'test_clock' => null,
                    'transfer_data' => null,
                    'trial_end' => null,
                    'trial_settings' => [
                        'end_behavior' => [
                            'missing_payment_method' => 'create_invoice',
                        ],
                    ],
                    'trial_start' => null,
                ],
            ],
        ];
    }

    protected function mockStripeClient(Customer $mockCustomer): void
    {
        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->customers = new class($mockCustomer)
        {
            private $mockCustomer;

            public function __construct($mockCustomer)
            {
                $this->mockCustomer = $mockCustomer;
            }

            public function retrieve()
            {
                return $this->mockCustomer;
            }
        };

        $this->app->instance(StripeClient::class, $mockStripeClient);
    }
}
