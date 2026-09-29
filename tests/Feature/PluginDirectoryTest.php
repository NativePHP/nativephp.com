<?php

namespace Tests\Feature;

use App\Enums\PluginCategory;
use App\Enums\PluginType;
use App\Features\ShowPlugins;
use App\Livewire\PluginDirectory;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $media = Plugin::factory()->approved()->categories(PluginCategory::Media)->create();
        Plugin::factory()->approved()->categories(PluginCategory::Payments)->create();

        Livewire::test(PluginDirectory::class)
            ->set('category', PluginCategory::Media->value)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$media->id]);
    }

    public function test_category_filter_finds_a_plugin_under_each_of_its_categories(): void
    {
        $scanner = Plugin::factory()->approved()->categories(PluginCategory::Media, PluginCategory::System)->create();
        Plugin::factory()->approved()->categories(PluginCategory::Payments)->create();

        Livewire::test(PluginDirectory::class)
            ->set('category', PluginCategory::Media->value)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$scanner->id])
            ->set('category', PluginCategory::System->value)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$scanner->id]);
    }

    public function test_category_filter_uncategorized_bucket_returns_only_null_category_plugins(): void
    {
        $uncategorized = Plugin::factory()->approved()->create(['categories' => null]);
        Plugin::factory()->approved()->categories(PluginCategory::Media)->create();

        Livewire::test(PluginDirectory::class)
            ->set('category', PluginDirectory::CATEGORY_UNCATEGORIZED)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$uncategorized->id]);
    }

    public function test_category_filter_uncategorized_bucket_includes_plugins_whose_categories_were_all_removed(): void
    {
        $emptied = Plugin::factory()->approved()->create(['categories' => []]);
        Plugin::factory()->approved()->categories(PluginCategory::Media)->create();

        Livewire::test(PluginDirectory::class)
            ->set('category', PluginDirectory::CATEGORY_UNCATEGORIZED)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$emptied->id]);
    }

    public function test_unfiltered_view_does_not_hide_uncategorized_plugins(): void
    {
        Plugin::factory()->approved()->create(['categories' => null]);
        Plugin::factory()->approved()->categories(PluginCategory::Media)->create();

        Livewire::test(PluginDirectory::class)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->count() === 2);
    }

    public function test_mobile_version_filter_buckets_by_major_version(): void
    {
        $v4 = Plugin::factory()->approved()->mobileVersions('4.2')->create();
        Plugin::factory()->approved()->mobileVersions('3.1')->create();

        Livewire::test(PluginDirectory::class)
            ->set('mobileVersion', '4')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$v4->id]);
    }

    public function test_mobile_version_filter_finds_a_plugin_under_each_major_version_it_supports(): void
    {
        $both = Plugin::factory()->approved()->mobileVersions('3.0', '4.0')->create();
        $v3 = Plugin::factory()->approved()->mobileVersions('3.2.1')->create();
        $v4 = Plugin::factory()->approved()->mobileVersions('4.5.2')->create();

        Livewire::test(PluginDirectory::class)
            ->set('mobileVersion', '3')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->sort()->values()->all() === [$both->id, $v3->id])
            ->set('mobileVersion', '4')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->sort()->values()->all() === [$both->id, $v4->id]);
    }

    public function test_mobile_version_filter_leaves_out_plugins_without_versions(): void
    {
        Plugin::factory()->approved()->create(['mobile_versions' => null]);
        $v4 = Plugin::factory()->approved()->mobileVersions('4.0')->create();

        Livewire::test(PluginDirectory::class)
            ->set('mobileVersion', '4')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->pluck('id')->all() === [$v4->id]);
    }

    #[DataProvider('retiredMobileVersionValues')]
    public function test_retired_or_unknown_mobile_version_query_param_is_ignored(string $value): void
    {
        Plugin::factory()->approved()->create(['mobile_versions' => null]);
        Plugin::factory()->approved()->mobileVersions('4.0')->create();

        Livewire::withQueryParams(['mobileVersion' => $value])
            ->test(PluginDirectory::class)
            ->assertSet('mobileVersion', '')
            ->assertViewHas('plugins', fn ($plugins) => $plugins->count() === 2);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function retiredMobileVersionValues(): array
    {
        return [
            'the old unspecified bucket' => ['unspecified'],
            'an older major version' => ['2'],
            'nonsense' => ['bogus'],
        ];
    }

    public function test_mobile_version_filter_options_are_the_configured_major_versions(): void
    {
        Livewire::test(PluginDirectory::class)
            ->assertViewHas('mobileVersionOptions', ['4', '3'])
            ->assertSee('NativePHP 4.x')
            ->assertSee('NativePHP 3.x')
            ->assertDontSee('Version Unspecified');
    }

    public function test_unfiltered_view_does_not_hide_plugins_without_mobile_versions(): void
    {
        Plugin::factory()->approved()->create(['mobile_versions' => null]);
        Plugin::factory()->approved()->mobileVersions('4.0')->create();

        Livewire::test(PluginDirectory::class)
            ->assertViewHas('plugins', fn ($plugins) => $plugins->count() === 2);
    }

    public function test_plugin_card_shows_a_pill_for_each_supported_mobile_version(): void
    {
        $plugin = Plugin::factory()->approved()->mobileVersions('4.5.2', '3.0')->create();

        $html = view('components.plugin-card', ['plugin' => $plugin])->render();

        $this->assertMatchesRegularExpression(
            '/Works with NativePHP Mobile 3\.x from 3\.0.*Works with NativePHP Mobile 4\.x from 4\.5\.2/s',
            $html,
        );
    }

    public function test_plugin_card_marks_mobile_versions_with_a_phone_icon(): void
    {
        $plugin = Plugin::factory()->approved()->mobileVersions('4.0')->create();

        $html = view('components.plugin-card', ['plugin' => $plugin])->render();

        $this->assertStringContainsString(
            Blade::render('<x-icons.device-mobile-phone class="h-3 shrink-0" aria-hidden="true" />'),
            $html,
        );
    }

    public function test_plugin_card_has_no_mobile_version_pills_without_versions(): void
    {
        $plugin = Plugin::factory()->approved()->create(['mobile_versions' => null]);

        $html = view('components.plugin-card', ['plugin' => $plugin])->render();

        $this->assertStringNotContainsString('Works with NativePHP Mobile', $html);
    }

    public function test_combining_type_category_and_mobile_version_filters(): void
    {
        $match = Plugin::factory()->approved()->paid()->categories(PluginCategory::Analytics)->mobileVersions('4.0')->create();
        Plugin::factory()->approved()->free()->categories(PluginCategory::Analytics)->mobileVersions('4.0')->create();
        Plugin::factory()->approved()->paid()->categories(PluginCategory::Media)->mobileVersions('4.0')->create();
        Plugin::factory()->approved()->paid()->categories(PluginCategory::Analytics)->mobileVersions('3.0')->create();

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
