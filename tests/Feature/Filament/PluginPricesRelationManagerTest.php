<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\RelationManagers\PricesRelationManager;
use App\Models\Plugin;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginPricesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_pricing_table_lists_an_ultra_price(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create(['is_official' => false]);
        $regularPrice = PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        $ultraPrice = PluginPrice::factory()->ultra()->amount(7000)->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(PricesRelationManager::class, [
                'ownerRecord' => $plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$regularPrice, $ultraPrice])
            ->assertSee('$70.00');
    }

    public function test_admin_can_add_an_ultra_price(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create(['is_official' => false]);

        Livewire::actingAs($this->admin)
            ->test(PricesRelationManager::class, [
                'ownerRecord' => $plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->callTableAction('create', data: [
                'tier' => 'ultra',
                'amount' => 7000,
                'currency' => 'USD',
                'is_active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('plugin_prices', [
            'plugin_id' => $plugin->id,
            'tier' => 'ultra',
            'amount' => 7000,
        ]);
    }
}
