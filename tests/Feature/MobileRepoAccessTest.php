<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Cashier\Subscription as CashierSubscription;
use Tests\TestCase;

class MobileRepoAccessTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_test_max_yearly';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);

        Cache::flush();
    }

    // ========================================
    // hasMobileRepoAccess() Unit Tests
    // ========================================

    public function test_user_with_active_max_license_has_mobile_repo_access(): void
    {
        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'max',
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        $this->assertTrue($user->hasMobileRepoAccess());
    }

    public function test_ultra_subscriber_before_cutoff_has_mobile_repo_access(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-01-15 00:00:00',
        ]);

        $this->assertTrue($user->hasMobileRepoAccess());
    }

    public function test_ultra_subscriber_after_cutoff_does_not_have_mobile_repo_access(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-02-01 00:00:00',
        ]);

        $this->assertFalse($user->hasMobileRepoAccess());
    }

    public function test_ultra_subscriber_on_cutoff_date_does_not_have_mobile_repo_access(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-02-01 12:00:00',
        ]);

        $this->assertFalse($user->hasMobileRepoAccess());
    }

    public function test_user_without_license_or_subscription_does_not_have_mobile_repo_access(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasMobileRepoAccess());
    }

    public function test_user_with_inactive_subscription_does_not_have_mobile_repo_access(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->canceled()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2025-12-01 00:00:00',
        ]);

        $this->assertFalse($user->hasMobileRepoAccess());
    }

    // ========================================
    // Integrations Page Visibility Tests
    // ========================================

    public function test_ultra_subscriber_before_cutoff_sees_mobile_repo_banner(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-01-15 00:00:00',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertSee('nativephp/mobile');
        $response->assertSee('Repo Access');
    }

    public function test_ultra_subscriber_after_cutoff_does_not_see_mobile_repo_banner(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 404)]);

        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-02-15 00:00:00',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertDontSee('nativephp/mobile</a> Repo Access', false);
    }

    // ========================================
    // Request Access Controller Tests
    // ========================================

    public function test_ultra_subscriber_after_cutoff_cannot_request_repo_access(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_'.uniqid(),
            'github_username' => 'testuser',
            'github_id' => '123456',
        ]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-02-15 00:00:00',
        ]);

        $response = $this->actingAs($user)
            ->post('/dashboard/github/request-access');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_ultra_subscriber_before_cutoff_can_request_repo_access(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile/collaborators/testuser' => Http::response([], 201),
        ]);

        $user = User::factory()->create([
            'stripe_id' => 'cus_'.uniqid(),
            'github_username' => 'testuser',
            'github_id' => '123456',
        ]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-01-15 00:00:00',
        ]);

        $response = $this->actingAs($user)
            ->post('/dashboard/github/request-access');

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->mobile_repo_access_granted_at);
    }

    // ========================================
    // Cleanup Command Tests
    // ========================================

    public function test_cleanup_command_removes_access_for_post_cutoff_ultra_subscriber(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile/collaborators/testuser' => Http::response([], 204),
        ]);

        $user = User::factory()->create([
            'stripe_id' => 'cus_'.uniqid(),
            'github_username' => 'testuser',
            'github_id' => '123456',
            'mobile_repo_access_granted_at' => now()->subDays(10),
        ]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-02-15 00:00:00',
        ]);

        $this->artisan('github:remove-expired-access')
            ->assertExitCode(0);

        $this->assertNull($user->fresh()->mobile_repo_access_granted_at);
    }

    public function test_cleanup_command_retains_access_for_pre_cutoff_ultra_subscriber(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_'.uniqid(),
            'github_username' => 'testuser',
            'github_id' => '123456',
            'mobile_repo_access_granted_at' => now()->subDays(10),
        ]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
            'created_at' => '2026-01-15 00:00:00',
        ]);

        $this->artisan('github:remove-expired-access')
            ->assertExitCode(0);

        $this->assertNotNull($user->fresh()->mobile_repo_access_granted_at);
    }
}
