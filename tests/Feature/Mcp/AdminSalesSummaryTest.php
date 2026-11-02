<?php

namespace Tests\Feature\Mcp;

use App\Enums\Subscription as SubscriptionPlan;
use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use Tests\Concerns\InteractsWithMcpOAuth;
use Tests\TestCase;

class AdminSalesSummaryTest extends TestCase
{
    use InteractsWithMcpOAuth;
    use RefreshDatabase;

    private const COMPED_ULTRA_PRICE_ID = 'price_test_ultra_comped';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureMcpOAuthKeys();
        $this->admin = User::factory()->create(['email' => 'admin-sales@nativephp.com']);
        config([
            'filament.users' => [$this->admin->email],
            'subscriptions.plans.max.stripe_price_id_comped' => self::COMPED_ULTRA_PRICE_ID,
            'subscriptions.plans.max.stripe_price_id_monthly' => 'price_max_monthly',
            'subscriptions.plans.max.stripe_price_id_discounted' => 'price_max_discounted',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function salesSummary(array $arguments = []): array
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-sales-summary', $arguments)->assertOk();
        $this->assertFalse($response->json('result.isError'), (string) data_get($response->json(), 'result.content.0.text'));

        return json_decode((string) data_get($response->json(), 'result.content.0.text'), true);
    }

    private function createPaidUltraSubscription(User $user, int $pricePaid, ?\DateTimeInterface $createdAt = null): Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);
        $priceId = SubscriptionPlan::Max->stripePriceId();

        $subscription = Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => $priceId,
                'is_comped' => false,
                'price_paid' => $pricePaid,
                'created_at' => $createdAt ?? now(),
                'updated_at' => $createdAt ?? now(),
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createCompedUltraSubscription(User $user): Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => self::COMPED_ULTRA_PRICE_ID,
                'is_comped' => false,
                'price_paid' => 0,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => self::COMPED_ULTRA_PRICE_ID,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    public function test_sales_summary_breakdown_includes_plugins_products_and_ultra(): void
    {
        $buyer = User::factory()->create();
        $plugin = Plugin::factory()->approved()->create(['name' => 'acme/camera']);
        $product = Product::factory()->create(['name' => 'Plugin Dev Kit']);

        PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'plugin_id' => $plugin->id,
            'price_paid' => 2500,
            'currency' => 'USD',
            'is_grandfathered' => false,
            'purchased_at' => now()->subDays(2),
        ]);
        PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'plugin_id' => $plugin->id,
            'price_paid' => 0,
            'currency' => 'USD',
            'is_grandfathered' => true,
            'purchased_at' => now()->subDays(1),
        ]);

        ProductLicense::factory()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'price_paid' => 9900,
            'currency' => 'USD',
            'is_comped' => false,
            'purchased_at' => now()->subDays(3),
        ]);
        ProductLicense::factory()->create([
            'user_id' => User::factory(),
            'product_id' => $product->id,
            'price_paid' => 0,
            'currency' => 'USD',
            'is_comped' => true,
            'purchased_at' => now()->subDay(),
        ]);

        $this->createPaidUltraSubscription($buyer, 35000, now()->subDays(5));
        $this->createCompedUltraSubscription(User::factory()->create());
        $this->createPaidUltraSubscription(User::factory()->create(), 35000, now()->subDays(400));

        License::factory()->withoutSubscriptionItem()->active()->mini()->create([
            'user_id' => $buyer->id,
            'created_at' => now()->subDays(4),
        ]);

        $payload = $this->salesSummary(['days' => 30]);

        $this->assertSame(30, $payload['days']);
        $this->assertFalse($payload['all_time']);
        $this->assertNotNull($payload['since']);
        $this->assertSame(2500 + 9900 + 35000, $payload['total_revenue_cents']);

        $bySource = collect($payload['breakdown'])->keyBy('source');
        $this->assertSame(2500, $bySource['plugins']['revenue_cents']);
        $this->assertSame(1, $bySource['plugins']['sales_count']);
        $this->assertSame(9900, $bySource['products']['revenue_cents']);
        $this->assertSame(1, $bySource['products']['sales_count']);
        $this->assertSame(35000, $bySource['ultra_subscriptions']['revenue_cents']);
        $this->assertSame(1, $bySource['ultra_subscriptions']['sales_count']);
        $this->assertArrayHasKey('caveat', $bySource['ultra_subscriptions']);

        $this->assertSame(2, $payload['ultra']['active_count']); // recent + old paid Ultra still active
        $this->assertSame(1, $payload['ultra']['active_comped_count']);
        $this->assertSame(2, $payload['ultra']['created_in_window']); // paid + comped in window; old paid excluded
        $this->assertSame(35000, $payload['ultra']['revenue_cents_in_window']);

        $this->assertSame(1, $payload['comped']['plugin_count']);
        $this->assertSame(1, $payload['comped']['product_count']);

        $this->assertSame(2500, $payload['plugin_license_revenue'][0]['revenue_cents']);
        $this->assertSame('acme/camera', $payload['top_plugins'][0]['plugin']);

        $byProductNames = collect($payload['by_product'])->pluck('product_name');
        $this->assertTrue($byProductNames->contains('acme/camera'));
        $this->assertTrue($byProductNames->contains('Plugin Dev Kit'));

        $this->assertNotEmpty($payload['nativephp_licenses_created']);
        $this->assertGreaterThanOrEqual(1, $payload['active_nativephp_licenses']);
        $this->assertStringContainsString('renewals', $payload['note']);
        $this->assertStringContainsString('sales_view', $payload['note']);
        $this->assertStringContainsString('Cashier', $payload['note']);
    }

    public function test_all_time_includes_old_ultra_and_sales(): void
    {
        $buyer = User::factory()->create();
        PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'price_paid' => 1000,
            'currency' => 'USD',
            'purchased_at' => now()->subYears(3),
        ]);
        $this->createPaidUltraSubscription($buyer, 35000, now()->subYears(2));

        $payload = $this->salesSummary(['all_time' => true]);

        $this->assertTrue($payload['all_time']);
        $this->assertNull($payload['days']);
        $this->assertNull($payload['since']);
        $this->assertSame(1000 + 35000, $payload['total_revenue_cents']);
        $this->assertSame(35000, $payload['ultra']['revenue_cents_in_window']);
    }

    public function test_days_window_can_exceed_one_year(): void
    {
        $buyer = User::factory()->create();
        PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'price_paid' => 1500,
            'currency' => 'USD',
            'purchased_at' => now()->subDays(500),
        ]);
        PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'price_paid' => 999,
            'currency' => 'USD',
            'purchased_at' => now()->subDays(800),
        ]);

        $payload = $this->salesSummary(['days' => 600]);

        $this->assertSame(600, $payload['days']);
        $this->assertSame(1500, $payload['total_revenue_cents']);
    }
}
