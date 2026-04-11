<?php

namespace Tests\Feature;

use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PluginWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ping_event_succeeds_for_unapproved_plugin(): void
    {
        $plugin = Plugin::factory()->create();

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'ping']
        );

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'pong']);
    }

    #[Test]
    public function ping_event_succeeds_for_approved_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'ping']
        );

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'pong']);
    }

    #[Test]
    public function non_ping_event_returns_403_for_unapproved_plugin(): void
    {
        $plugin = Plugin::factory()->create();

        $response = $this->postJson(
            route('webhooks.plugins', $plugin->webhook_secret),
            [],
            ['X-GitHub-Event' => 'push']
        );

        $response->assertForbidden()
            ->assertJson(['error' => 'Plugin is not approved']);
    }

    #[Test]
    public function invalid_secret_returns_404(): void
    {
        $response = $this->postJson(
            route('webhooks.plugins', 'invalid-secret'),
            [],
            ['X-GitHub-Event' => 'ping']
        );

        $response->assertNotFound();
    }
}
