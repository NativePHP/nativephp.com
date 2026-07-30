<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginType;
use App\Filament\Resources\ThirdPartySaleResource\Pages\ListThirdPartySales;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThirdPartySaleResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    private function createThirdPartyPlugin(): Plugin
    {
        $developerAccount = DeveloperAccount::factory()->create();

        return Plugin::factory()->paid()->create([
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
    }

    public function test_list_page_renders_successfully(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        PluginLicense::factory()->count(2)->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful();
    }

    public function test_shows_sales_with_missing_payout(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$license])
            ->assertSee('Missing');
    }

    public function test_shows_payout_status_when_payout_exists(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        PluginPayout::factory()->transferred()->create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $plugin->developer_account_id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful()
            ->assertSee('Transferred');
    }

    public function test_excludes_official_plugin_sales(): void
    {
        $officialPlugin = Plugin::factory()->paid()->create(['is_official' => true]);
        $officialSale = PluginLicense::factory()->create(['plugin_id' => $officialPlugin->id]);

        $thirdPartyPlugin = $this->createThirdPartyPlugin();
        $thirdPartySale = PluginLicense::factory()->create(['plugin_id' => $thirdPartyPlugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$thirdPartySale])
            ->assertCanNotSeeTableRecords([$officialSale]);
    }

    public function test_excludes_free_plugin_sales(): void
    {
        $freePlugin = Plugin::factory()->create([
            'is_official' => false,
            'type' => PluginType::Free,
        ]);
        $freeSale = PluginLicense::factory()->create(['plugin_id' => $freePlugin->id]);

        $paidPlugin = $this->createThirdPartyPlugin();
        $paidSale = PluginLicense::factory()->create(['plugin_id' => $paidPlugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$paidSale])
            ->assertCanNotSeeTableRecords([$freeSale]);
    }

    public function test_excludes_grandfathered_licenses(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $granted = PluginLicense::factory()->grandfathered()->create(['plugin_id' => $plugin->id]);
        $sale = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$sale])
            ->assertCanNotSeeTableRecords([$granted]);
    }

    public function test_filters_sales_missing_payouts(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $missingPayout = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);

        $withPayout = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);
        PluginPayout::factory()->create([
            'plugin_license_id' => $withPayout->id,
            'developer_account_id' => $plugin->developer_account_id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->filterTable('missing_payout', true)
            ->assertCanSeeTableRecords([$missingPayout])
            ->assertCanNotSeeTableRecords([$withPayout]);
    }
}
