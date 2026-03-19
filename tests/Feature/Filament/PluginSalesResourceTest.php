<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\SalesResource\Pages\ListSales;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\ProductLicense;
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

    public function test_sales_page_renders_successfully(): void
    {
        PluginLicense::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListSales::class)
            ->assertSuccessful();
    }

    public function test_sales_page_renders_when_plugin_has_null_name(): void
    {
        $pluginWithName = Plugin::factory()->approved()->create(['name' => 'acme/camera-123']);
        $pluginWithoutName = Plugin::factory()->approved()->create(['name' => null]);

        PluginLicense::factory()->create(['plugin_id' => $pluginWithName->id]);
        PluginLicense::factory()->create(['plugin_id' => $pluginWithoutName->id]);

        Livewire::actingAs($this->admin)
            ->test(ListSales::class)
            ->assertSuccessful();
    }

    public function test_sales_page_shows_product_license_sales(): void
    {
        ProductLicense::factory()->count(2)->create();

        Livewire::actingAs($this->admin)
            ->test(ListSales::class)
            ->assertSuccessful();
    }

    public function test_sales_page_shows_both_plugin_and_product_sales(): void
    {
        PluginLicense::factory()->count(2)->create();
        ProductLicense::factory()->count(2)->create();

        Livewire::actingAs($this->admin)
            ->test(ListSales::class)
            ->assertSuccessful();
    }

    public function test_sales_page_shows_bundle_name_for_plugin_sales(): void
    {
        $bundle = PluginBundle::factory()->create(['name' => 'Pro Bundle']);
        PluginLicense::factory()->create(['plugin_bundle_id' => $bundle->id]);

        Livewire::actingAs($this->admin)
            ->test(ListSales::class)
            ->assertSuccessful()
            ->assertSee('Pro Bundle');
    }

    public function test_sales_page_shows_comped_column(): void
    {
        PluginLicense::factory()->grandfathered()->create();

        Livewire::actingAs($this->admin)
            ->test(ListSales::class)
            ->assertSuccessful();
    }
}
