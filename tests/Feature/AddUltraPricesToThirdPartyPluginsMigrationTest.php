<?php

namespace Tests\Feature;

use App\Enums\PluginTier;
use App\Enums\PriceTier;
use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddUltraPricesToThirdPartyPluginsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_third_party_plugins_get_the_ultra_price_for_their_tier(): void
    {
        $bronze = Plugin::factory()->paid()->create(['is_official' => false, 'tier' => PluginTier::Bronze]);
        $silver = Plugin::factory()->paid()->create(['is_official' => false, 'tier' => PluginTier::Silver]);
        $gold = Plugin::factory()->paid()->create(['is_official' => false, 'tier' => PluginTier::Gold]);

        $this->migration()->up();

        $this->assertSame(2000, $this->ultraPriceOf($bronze)->amount);
        $this->assertSame(3500, $this->ultraPriceOf($silver)->amount);
        $this->assertSame(7000, $this->ultraPriceOf($gold)->amount);

        $this->assertTrue($this->ultraPriceOf($gold)->is_active);
        $this->assertSame('USD', $this->ultraPriceOf($gold)->currency);
    }

    public function test_official_plugins_and_plugins_without_a_tier_are_left_alone(): void
    {
        $official = Plugin::factory()->paid()->create(['is_official' => true, 'tier' => PluginTier::Gold]);
        $withoutTier = Plugin::factory()->paid()->create(['is_official' => false, 'tier' => null]);
        PluginPrice::factory()->regular()->amount(2200)->create(['plugin_id' => $withoutTier->id]);

        $this->migration()->up();

        $this->assertNull($this->ultraPriceOf($official));
        $this->assertNull($this->ultraPriceOf($withoutTier));
    }

    public function test_an_existing_ultra_price_is_not_duplicated_or_changed(): void
    {
        $plugin = Plugin::factory()->paid()->create(['is_official' => false, 'tier' => PluginTier::Gold]);
        PluginPrice::factory()->ultra()->amount(6000)->create(['plugin_id' => $plugin->id]);

        $this->migration()->up();

        $this->assertSame(1, $plugin->prices()->forTier(PriceTier::Ultra)->count());
        $this->assertSame(6000, $this->ultraPriceOf($plugin)->amount);
    }

    public function test_rolling_back_removes_the_ultra_prices(): void
    {
        $plugin = Plugin::factory()->paid()->create(['is_official' => false, 'tier' => PluginTier::Gold]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);

        $migration = $this->migration();
        $migration->up();
        $migration->down();

        $this->assertNull($this->ultraPriceOf($plugin));
        $this->assertSame(1, $plugin->prices()->count());
    }

    private function ultraPriceOf(Plugin $plugin): ?PluginPrice
    {
        return $plugin->prices()->forTier(PriceTier::Ultra)->first();
    }

    private function migration(): Migration
    {
        return require database_path('migrations/2026_10_01_124505_add_ultra_prices_to_third_party_plugins.php');
    }
}
