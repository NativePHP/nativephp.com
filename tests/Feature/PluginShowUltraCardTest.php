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

    private function createThirdPartyPluginWithUltraPrice(): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => false]);

        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->ultra()->amount(7000)->create(['plugin_id' => $plugin->id]);

        return $plugin;
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

    public function test_ultra_offer_shows_on_third_party_plugin_for_guest(): void
    {
        $plugin = $this->createThirdPartyPluginWithUltraPrice();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Get this plugin for $70 with Ultra')
            ->assertSee('save $29 on this plugin')
            ->assertSee('Learn more')
            ->assertSee(route('pricing'))
            ->assertDontSee('Included with Ultra')
            ->assertDontSee('pricing applied');
    }

    public function test_ultra_offer_shows_on_third_party_plugin_for_user_without_ultra(): void
    {
        $plugin = $this->createThirdPartyPluginWithUltraPrice();

        $this->actingAs(User::factory()->create())
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Get this plugin for $70 with Ultra');
    }

    public function test_ultra_subscriber_sees_the_ultra_price_instead_of_the_offer(): void
    {
        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::MAX_PRICE_ID]);

        $plugin = $this->createThirdPartyPluginWithUltraPrice();

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('Get this plugin for')
            ->assertSee('Ultra pricing applied')
            ->assertSeeInOrder(['line-through', '$99', '$70'], false);
    }

    public function test_ultra_offer_is_hidden_when_third_party_plugin_has_no_ultra_price(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => false]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('$99')
            ->assertDontSee('Get this plugin for');
    }

    public function test_ultra_offer_is_hidden_on_first_party_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => true]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->ultra()->amount(7000)->create(['plugin_id' => $plugin->id]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Included with Ultra')
            ->assertDontSee('Get this plugin for');
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
