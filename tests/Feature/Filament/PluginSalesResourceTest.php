<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginSalesResource\Pages\ListPluginSales;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginSalesResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_plugin_sales_page_renders_when_plugin_has_null_name(): void
    {
        $pluginWithName = Plugin::factory()->approved()->create(['name' => 'acme/camera-123']);
        $pluginWithoutName = Plugin::factory()->approved()->create(['name' => null]);

        PluginLicense::factory()->create(['plugin_id' => $pluginWithName->id]);
        PluginLicense::factory()->create(['plugin_id' => $pluginWithoutName->id]);

        Livewire::actingAs($this->admin)
            ->test(ListPluginSales::class)
            ->assertSuccessful();
    }

    public function test_plugin_sales_page_renders_successfully(): void
    {
        PluginLicense::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginSales::class)
            ->assertSuccessful();
    }
}
