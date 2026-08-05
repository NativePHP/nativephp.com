<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginShowUltraCardTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_1RoZk0AyFo6rlwXqjkLj4hZ0';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    public function test_ultra_card_shows_on_paid_first_party_plugin_for_guest(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => true]);
        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $plugin->id]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Included with Ultra')
            ->assertSee('Learn more')
            ->assertSee(route('pricing'));
    }

    public function test_ultra_card_is_hidden_on_free_first_party_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create(['is_official' => true]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('Included with Ultra');
    }

    public function test_ultra_card_is_hidden_on_third_party_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => false]);
        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $plugin->id]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('Included with Ultra');
    }

    public function test_ultra_subscriber_sees_dashboard_link(): void
    {
        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::MAX_PRICE_ID]);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => true]);
        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $plugin->id]);

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Included with Ultra')
            ->assertSee('Go to your dashboard')
            ->assertSee(route('customer.ultra.index'))
            ->assertDontSee('You can still purchase this plugin');
    }
}
