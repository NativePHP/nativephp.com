<?php

namespace Tests\Feature;

use App\Enums\PluginCategory;
use App\Enums\PluginType;
use App\Features\ShowPlugins;
use App\Livewire\PluginDirectory;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_plugin_directory_paginates_twelve_per_page(): void
    {
        Plugin::factory()->approved()->count(13)->create();

        Livewire::test(PluginDirectory::class)
            ->assertViewHas('plugins', function ($plugins) {
                return $plugins->count() === 12
                    && $plugins->lastPage() === 2;
            });
    }

    public function test_paid_plugin_card_shows_premium_badge(): void
    {
        // Rendered directly rather than through PluginDirectory: the page's
        // type filter now legitimately shows a "Paid" option label, so a
        // whole-page assertDontSee('Paid') would false-fail against it.
        $plugin = Plugin::factory()->approved()->paid()->create();

        $html = view('components.plugin-card', ['plugin' => $plugin])->render();

        $this->assertStringContainsString('Premium', $html);
        $this->assertStringNotContainsString('Paid', $html);
    }

    public function test_type_filter_narrows_to_free_plugins(): void
    {
        $free = Plugin::factory()->approved()->free()->create();
        Plugin::factory()->approved()->paid()->create();

        Livewire::test(PluginDirectory::class)
            ->set('type', PluginType::Free->value)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$free->id]);
    }

    public function test_type_filter_narrows_to_paid_plugins(): void
    {
        Plugin::factory()->approved()->free()->create();
        $paid = Plugin::factory()->approved()->paid()->create();

        Livewire::test(PluginDirectory::class)
            ->set('type', PluginType::Paid->value)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$paid->id]);
    }

    public function test_category_filter_narrows_to_matching_plugins(): void
    {
        $media = Plugin::factory()->approved()->category(PluginCategory::Media)->create();
        Plugin::factory()->approved()->category(PluginCategory::Payments)->create();

        Livewire::test(PluginDirectory::class)
            ->set('category', PluginCategory::Media->value)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$media->id]);
    }

    public function test_category_filter_uncategorized_bucket_returns_only_null_category_plugins(): void
    {
        $uncategorized = Plugin::factory()->approved()->create(['category' => null]);
        Plugin::factory()->approved()->category(PluginCategory::Media)->create();

        Livewire::test(PluginDirectory::class)
            ->set('category', PluginDirectory::CATEGORY_UNCATEGORIZED)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$uncategorized->id]);
    }

    public function test_unfiltered_view_does_not_hide_uncategorized_plugins(): void
    {
        Plugin::factory()->approved()->create(['category' => null]);
        Plugin::factory()->approved()->category(PluginCategory::Media)->create();

        Livewire::test(PluginDirectory::class)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->count() === 2);
    }

    public function test_mobile_version_filter_buckets_by_major_version(): void
    {
        $v4 = Plugin::factory()->approved()->create(['mobile_min_version' => '4.2.0']);
        Plugin::factory()->approved()->create(['mobile_min_version' => '3.1.0']);

        Livewire::test(PluginDirectory::class)
            ->set('mobileVersion', '4')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$v4->id]);
    }

    public function test_mobile_version_filter_matches_exact_major_version_string(): void
    {
        $v4 = Plugin::factory()->approved()->create(['mobile_min_version' => '4']);
        Plugin::factory()->approved()->create(['mobile_min_version' => '3.1.0']);

        Livewire::test(PluginDirectory::class)
            ->set('mobileVersion', '4')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$v4->id]);
    }

    public function test_mobile_version_filter_unspecified_bucket_returns_only_null_versions(): void
    {
        $unspecified = Plugin::factory()->approved()->create(['mobile_min_version' => null]);
        Plugin::factory()->approved()->create(['mobile_min_version' => '4.0.0']);

        Livewire::test(PluginDirectory::class)
            ->set('mobileVersion', PluginDirectory::MOBILE_VERSION_UNSPECIFIED)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$unspecified->id]);
    }

    public function test_unfiltered_view_does_not_hide_plugins_without_mobile_min_version(): void
    {
        Plugin::factory()->approved()->create(['mobile_min_version' => null]);
        Plugin::factory()->approved()->create(['mobile_min_version' => '4.0.0']);

        Livewire::test(PluginDirectory::class)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->count() === 2);
    }

    public function test_combining_type_category_and_mobile_version_filters(): void
    {
        $match = Plugin::factory()->approved()->paid()->category(PluginCategory::Analytics)->create([
            'mobile_min_version' => '4.0.0',
        ]);
        Plugin::factory()->approved()->free()->category(PluginCategory::Analytics)->create([
            'mobile_min_version' => '4.0.0',
        ]);
        Plugin::factory()->approved()->paid()->category(PluginCategory::Media)->create([
            'mobile_min_version' => '4.0.0',
        ]);
        Plugin::factory()->approved()->paid()->category(PluginCategory::Analytics)->create([
            'mobile_min_version' => '3.0.0',
        ]);

        Livewire::test(PluginDirectory::class)
            ->set('type', PluginType::Paid->value)
            ->set('category', PluginCategory::Analytics->value)
            ->set('mobileVersion', '4')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$match->id]);
    }

    public function test_clear_filters_resets_type_category_and_mobile_version(): void
    {
        Plugin::factory()->approved()->count(2)->create();

        Livewire::test(PluginDirectory::class)
            ->set('type', PluginType::Paid->value)
            ->set('category', PluginCategory::Media->value)
            ->set('mobileVersion', '4')
            ->call('clearFilters')
            ->assertSet('type', '')
            ->assertSet('category', '')
            ->assertSet('mobileVersion', '')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->count() === 2);
    }

    public function test_invalid_type_query_param_does_not_crash_the_page(): void
    {
        Plugin::factory()->approved()->count(2)->create();

        $this->get(route('plugins.marketplace', ['type' => 'bogus']))
            ->assertOk()
            ->assertSee('All Types');
    }

    public function test_invalid_category_query_param_does_not_crash_the_page(): void
    {
        Plugin::factory()->approved()->count(2)->create();

        $this->get(route('plugins.marketplace', ['category' => 'bogus']))
            ->assertOk()
            ->assertSee('All Categories');
    }
}
