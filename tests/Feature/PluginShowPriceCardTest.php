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

class PluginShowPriceCardTest extends TestCase
{
    use RefreshDatabase;

    private const PRO_PRICE_ID = 'price_1RoZeVAyFo6rlwXqtnOViUCf';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_price_card_omits_the_price_label(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create();

        PluginPrice::factory()->regular()->amount(9900)->create([
            'plugin_id' => $plugin->id,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('$99')
            ->assertSee('One-time purchase')
            ->assertDontSee('>Price</p>', false);
    }

    public function test_discounted_price_card_shows_strike_through_before_current_price(): void
    {
        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginPrice::factory()->regular()->amount(9900)->create([
            'plugin_id' => $plugin->id,
        ]);
        PluginPrice::factory()->subscriber()->amount(4900)->create([
            'plugin_id' => $plugin->id,
        ]);

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('pricing applied')
            ->assertDontSee('>Price</p>', false)
            ->assertSeeInOrder(['line-through', '$99', '$49'], false);
    }

    public function test_plugin_details_section_is_collapsible_and_collapsed_by_default(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Plugin Details')
            ->assertSee('x-data="{ open: false }"', false)
            ->assertSee('x-collapse', false);
    }
}
