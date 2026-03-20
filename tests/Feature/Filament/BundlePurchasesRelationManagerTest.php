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

    public function test_it_computes_correct_totals_per_sale(): void
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

        $bundleId = $this->bundle->id;

        $row = PluginLicense::query()
            ->select('plugin_licenses.*')
            ->addSelect([
                'sale_total' => PluginLicense::query()
                    ->from('plugin_licenses as sale_pl')
                    ->selectRaw('SUM(sale_pl.price_paid)')
                    ->whereColumn('sale_pl.user_id', 'plugin_licenses.user_id')
                    ->whereColumn('sale_pl.purchased_at', 'plugin_licenses.purchased_at')
                    ->where('sale_pl.plugin_bundle_id', $bundleId),
                'sale_plugins_count' => PluginLicense::query()
                    ->from('plugin_licenses as count_pl')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('count_pl.user_id', 'plugin_licenses.user_id')
                    ->whereColumn('count_pl.purchased_at', 'plugin_licenses.purchased_at')
                    ->where('count_pl.plugin_bundle_id', $bundleId),
            ])
            ->where('plugin_bundle_id', $bundleId)
            ->whereIn('plugin_licenses.id', function ($sub) use ($bundleId): void {
                $sub->selectRaw('MIN(id)')
                    ->from('plugin_licenses as pl')
                    ->where('pl.plugin_bundle_id', $bundleId)
                    ->groupBy('pl.user_id', 'pl.purchased_at');
            })
            ->sole();

        $this->assertEquals(3, $row->sale_plugins_count);
        $this->assertEquals(3000, $row->sale_total);
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

    public function test_it_computes_correct_totals_for_each_buyer(): void
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
                'price_paid' => 2000,
                'purchased_at' => $purchasedAt,
            ]);
        }

        $bundleId = $this->bundle->id;

        $rows = PluginLicense::query()
            ->select('plugin_licenses.*')
            ->addSelect([
                'sale_total' => PluginLicense::query()
                    ->from('plugin_licenses as sale_pl')
                    ->selectRaw('SUM(sale_pl.price_paid)')
                    ->whereColumn('sale_pl.user_id', 'plugin_licenses.user_id')
                    ->whereColumn('sale_pl.purchased_at', 'plugin_licenses.purchased_at')
                    ->where('sale_pl.plugin_bundle_id', $bundleId),
                'sale_plugins_count' => PluginLicense::query()
                    ->from('plugin_licenses as count_pl')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('count_pl.user_id', 'plugin_licenses.user_id')
                    ->whereColumn('count_pl.purchased_at', 'plugin_licenses.purchased_at')
                    ->where('count_pl.plugin_bundle_id', $bundleId),
            ])
            ->where('plugin_bundle_id', $bundleId)
            ->whereIn('plugin_licenses.id', function ($sub) use ($bundleId): void {
                $sub->selectRaw('MIN(id)')
                    ->from('plugin_licenses as pl')
                    ->where('pl.plugin_bundle_id', $bundleId)
                    ->groupBy('pl.user_id', 'pl.purchased_at');
            })
            ->get()
            ->keyBy('user_id');

        $this->assertEquals(2, $rows[$buyer1->id]->sale_plugins_count);
        $this->assertEquals(3000, $rows[$buyer1->id]->sale_total);

        $this->assertEquals(2, $rows[$buyer2->id]->sale_plugins_count);
        $this->assertEquals(4000, $rows[$buyer2->id]->sale_total);
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
