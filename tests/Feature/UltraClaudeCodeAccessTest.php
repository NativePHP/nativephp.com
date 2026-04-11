<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Jobs\RevokeTeamUserAccessJob;
use App\Listeners\StripeWebhookReceivedListener;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Subscription as CashierSubscription;
use Tests\TestCase;

class UltraClaudeCodeAccessTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_test_max_yearly';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    private function createUltraUser(): User
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
        ]);

        return $user;
    }

    // ========================================
    // Banner Visibility Tests
    // ========================================

    public function test_ultra_subscriber_sees_claude_plugins_banner(): void
    {
        Http::fake(['github.com/*' => Http::response([], 200)]);

        $user = $this->createUltraUser();

        $response = $this->actingAs($user)->get(route('customer.integrations'));

        $response->assertStatus(200);
        $response->assertSee('Repo Access');
    }

    public function test_plugin_dev_kit_license_holder_sees_claude_plugins_banner(): void
    {
        Http::fake(['github.com/*' => Http::response([], 200)]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['slug' => 'plugin-dev-kit']);
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('customer.integrations'));

        $response->assertStatus(200);
        $response->assertSee('Repo Access');
    }

    public function test_non_ultra_non_licensed_user_does_not_see_claude_plugins_banner(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('customer.integrations'));

        $response->assertStatus(200);
        $response->assertDontSee('Repo Access');
    }

    // ========================================
    // Request Access Tests
    // ========================================

    public function test_ultra_subscriber_can_request_claude_plugins_access(): void
    {
        Http::fake(['github.com/*' => Http::response([], 201)]);

        $user = $this->createUltraUser();
        $user->update(['github_username' => 'ultrauser']);

        $response = $this->actingAs($user)
            ->post(route('github.request-claude-plugins-access'));

        $response->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->claude_plugins_repo_access_granted_at);
    }

    public function test_non_ultra_non_licensed_user_cannot_request_claude_plugins_access(): void
    {
        $user = User::factory()->create(['github_username' => 'someuser']);

        $response = $this->actingAs($user)
            ->post(route('github.request-claude-plugins-access'));

        $response->assertSessionHas('error');
        $this->assertNull($user->fresh()->claude_plugins_repo_access_granted_at);
    }

    // ========================================
    // Subscription Revocation Tests
    // ========================================

    public function test_revoke_job_dispatched_when_subscription_deleted(): void
    {
        Queue::fake();

        $user = $this->createUltraUser();

        $event = new WebhookReceived([
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'customer' => $user->stripe_id,
                ],
            ],
        ]);

        $listener = new StripeWebhookReceivedListener;
        $listener->handle($event);

        Queue::assertPushed(RevokeTeamUserAccessJob::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }

    public function test_revoke_job_dispatched_when_subscription_canceled(): void
    {
        Queue::fake();

        $user = $this->createUltraUser();

        $event = new WebhookReceived([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'customer' => $user->stripe_id,
                    'status' => 'canceled',
                ],
                'previous_attributes' => [
                    'status' => 'active',
                ],
            ],
        ]);

        $listener = new StripeWebhookReceivedListener;
        $listener->handle($event);

        Queue::assertPushed(RevokeTeamUserAccessJob::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }

    public function test_revoke_job_not_dispatched_when_subscription_reactivated(): void
    {
        Queue::fake();

        $user = $this->createUltraUser();

        $event = new WebhookReceived([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'customer' => $user->stripe_id,
                    'status' => 'active',
                ],
                'previous_attributes' => [
                    'status' => 'canceled',
                ],
            ],
        ]);

        $listener = new StripeWebhookReceivedListener;
        $listener->handle($event);

        Queue::assertNotPushed(RevokeTeamUserAccessJob::class);
    }
}
