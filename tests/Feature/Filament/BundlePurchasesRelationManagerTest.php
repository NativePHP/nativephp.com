<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginBundleResource\RelationManagers\LicensesRelationManager;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BundlePurchasesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private PluginBundle $bundle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $this->bundle = PluginBundle::factory()->active()->create();
    }

    public function test_it_groups_bundle_licenses_into_a_single_sale_row(): void
    {
        $buyer = User::factory()->create();
        $purchasedAt = now();

        $plugins = Plugin::factory()->count(3)->approved()->create();
        $this->bundle->plugins()->attach($plugins->pluck('id'));

        foreach ($plugins as $plugin) {
            PluginLicense::factory()->create([
                'user_id' => $buyer->id,
                'plugin_id' => $plugin->id,
                'plugin_bundle_id' => $this->bundle->id,
                'price_paid' => 1000,
                'purchased_at' => $purchasedAt,
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(LicensesRelationManager::class, [
                'ownerRecord' => $this->bundle,
                'pageClass' => \App\Filament\Resources\PluginBundleResource\Pages\ViewPluginBundle::class,
            ])
            ->assertCanSeeTableRecords(
                PluginLicense::where('plugin_bundle_id', $this->bundle->id)
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MIN(id)')
                            ->from('plugin_licenses as pl')
                            ->where('pl.plugin_bundle_id', $this->bundle->id)
                            ->groupBy('pl.user_id', 'pl.purchased_at');
                    })
                    ->get()
            )
            ->assertCountTableRecords(1);
    }

    public function test_it_shows_separate_rows_for_different_sales(): void
    {
        $buyer1 = User::factory()->create();
        $buyer2 = User::factory()->create();
        $purchasedAt = now();

        $plugins = Plugin::factory()->count(2)->approved()->create();
        $this->bundle->plugins()->attach($plugins->pluck('id'));

        foreach ($plugins as $plugin) {
            PluginLicense::factory()->create([
                'user_id' => $buyer1->id,
                'plugin_id' => $plugin->id,
                'plugin_bundle_id' => $this->bundle->id,
                'price_paid' => 1500,
                'purchased_at' => $purchasedAt,
            ]);
        }

        foreach ($plugins as $plugin) {
            PluginLicense::factory()->create([
                'user_id' => $buyer2->id,
                'plugin_id' => $plugin->id,
                'plugin_bundle_id' => $this->bundle->id,
                'price_paid' => 1500,
                'purchased_at' => $purchasedAt,
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(LicensesRelationManager::class, [
                'ownerRecord' => $this->bundle,
                'pageClass' => \App\Filament\Resources\PluginBundleResource\Pages\ViewPluginBundle::class,
            ])
            ->assertCountTableRecords(2);
    }

    public function test_it_shows_grandfathered_status(): void
    {
        $buyer = User::factory()->create();
        $plugin = Plugin::factory()->approved()->create();
        $this->bundle->plugins()->attach($plugin);

        PluginLicense::factory()->grandfathered()->create([
            'user_id' => $buyer->id,
            'plugin_id' => $plugin->id,
            'plugin_bundle_id' => $this->bundle->id,
            'price_paid' => 0,
            'purchased_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(LicensesRelationManager::class, [
                'ownerRecord' => $this->bundle,
                'pageClass' => \App\Filament\Resources\PluginBundleResource\Pages\ViewPluginBundle::class,
            ])
            ->assertCountTableRecords(1);
    }
}
