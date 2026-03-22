<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class CustomerPurchasedPluginsIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
    }

    public function test_guest_cannot_view_purchased_plugins_page(): void
    {
        $response = $this->get('/dashboard/purchased-plugins');

        $response->assertRedirect('/login');
    }

    public function test_customer_can_view_purchased_plugins_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/purchased-plugins');

        $response->assertStatus(200);
        $response->assertSee('Purchased Plugins');
        $response->assertSee('Your Plugin Credentials');
    }

    public function test_customer_sees_empty_state_when_no_plugins(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/purchased-plugins');

        $response->assertStatus(200);
        $response->assertSee('No plugins yet');
        $response->assertSee('Browse Plugins');
    }

    public function test_customer_sees_their_purchased_plugins(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->approved()->create(['name' => 'acme/test-plugin-123']);

        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/purchased-plugins');

        $response->assertStatus(200);
        $response->assertSee('acme/test-plugin-123');
        $response->assertSee('Licensed');
    }

    public function test_customer_does_not_see_other_users_plugins(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $plugin1 = Plugin::factory()->approved()->create(['name' => 'acme/user1-plugin-111']);
        $plugin2 = Plugin::factory()->approved()->create(['name' => 'acme/user2-plugin-222']);

        PluginLicense::factory()->create([
            'user_id' => $user1->id,
            'plugin_id' => $plugin1->id,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $user2->id,
            'plugin_id' => $plugin2->id,
        ]);

        $response = $this->actingAs($user1)->get('/dashboard/purchased-plugins');

        $response->assertStatus(200);
        $response->assertSee('acme/user1-plugin-111');
        $response->assertDontSee('acme/user2-plugin-222');
    }

    public function test_plugin_credentials_section_displays(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/purchased-plugins');

        $response->assertStatus(200);
        $response->assertSee('Your Plugin Credentials');
        $response->assertSee('Composer');
    }
}
