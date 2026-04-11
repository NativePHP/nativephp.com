<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Mockery;
use Stripe\StripeClient;
use Tests\TestCase;

class BackfillSubscriptionPricesTest extends TestCase
{
    use RefreshDatabase;

    private function mockStripeInvoices(array $invoiceData): void
    {
        $invoiceList = Mockery::mock();
        $invoiceList->data = $invoiceData;

        $invoicesMock = Mockery::mock();
        $invoicesMock->shouldReceive('all')->andReturn($invoiceList);

        $stripeMock = Mockery::mock(StripeClient::class);
        $stripeMock->invoices = $invoicesMock;

        $this->app->bind(StripeClient::class, fn () => $stripeMock);
    }

    public function test_backfills_price_paid_from_stripe_invoices(): void
    {
        $subscription = Subscription::factory()->active()->create([
            'price_paid' => null,
        ]);

        $this->mockStripeInvoices([(object) ['total' => 9900]]);

        $this->artisan('subscriptions:backfill-prices')
            ->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'price_paid' => 9900,
        ]);
    }

    public function test_skips_subscriptions_that_already_have_price_paid(): void
    {
        Subscription::factory()->active()->create([
            'price_paid' => 4900,
        ]);

        $this->artisan('subscriptions:backfill-prices')
            ->expectsOutput('No subscriptions need backfilling.')
            ->assertSuccessful();
    }

    public function test_handles_stripe_api_errors_gracefully(): void
    {
        $subscription = Subscription::factory()->active()->create([
            'price_paid' => null,
        ]);

        $invoicesMock = Mockery::mock();
        $invoicesMock->shouldReceive('all')
            ->andThrow(new \Exception('Stripe API error'));

        $stripeMock = Mockery::mock(StripeClient::class);
        $stripeMock->invoices = $invoicesMock;

        $this->app->bind(StripeClient::class, fn () => $stripeMock);

        $this->artisan('subscriptions:backfill-prices')
            ->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'price_paid' => null,
        ]);
    }

    public function test_handles_negative_invoice_total(): void
    {
        $subscription = Subscription::factory()->active()->create([
            'price_paid' => null,
        ]);

        $this->mockStripeInvoices([(object) ['total' => -500]]);

        $this->artisan('subscriptions:backfill-prices')
            ->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'price_paid' => 0,
        ]);
    }

    public function test_handles_empty_invoice_list(): void
    {
        $subscription = Subscription::factory()->active()->create([
            'price_paid' => null,
        ]);

        $this->mockStripeInvoices([]);

        $this->artisan('subscriptions:backfill-prices')
            ->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'price_paid' => null,
        ]);
    }
}
