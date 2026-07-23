<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginShowSeoMetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_plugin_show_sets_title_and_opengraph_meta(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'display_name' => 'Awesome Plugin',
            'description' => 'Does awesome things.',
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('<title>Awesome Plugin - Plugin - NativePHP</title>', false)
            ->assertSee('<meta property="og:title" content="Awesome Plugin">', false)
            ->assertSee('<meta property="og:description" content="Does awesome things.">', false)
            ->assertSee('<meta name="twitter:title" content="Awesome Plugin">', false)
            ->assertSee('<meta name="twitter:description" content="Does awesome things.">', false);
    }

    public function test_plugin_show_uses_generated_og_image(): void
    {
        $ogImage = 'https://nativephp.com.test/storage/og-images/plugins/1.png';

        $plugin = Plugin::factory()->approved()->create([
            'display_name' => 'Imaged Plugin',
            'og_image' => $ogImage,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('<meta property="og:image" content="'.e($ogImage).'">', false)
            ->assertSee('<meta name="twitter:image" content="'.e($ogImage).'">', false);
    }

    public function test_plugin_show_falls_back_to_default_og_image_when_none_generated(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'display_name' => 'No Image Plugin',
            'og_image' => null,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('og-images/plugins/')
            ->assertSee(config('seotools.opengraph.defaults.images.0'));
    }

    public function test_plugin_show_falls_back_to_generated_description(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'display_name' => 'No Description Plugin',
            'description' => null,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('No Description Plugin is a plugin for NativePHP Mobile.');
    }

    public function test_plugin_license_sets_seo_meta(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'display_name' => 'Paid Plugin',
            'description' => 'A premium plugin.',
            'license_html' => '<p>License terms</p>',
        ]);

        PluginPrice::factory()->regular()->create([
            'plugin_id' => $plugin->id,
            'amount' => 2999,
        ]);

        $this->get(route('plugins.license', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('<title>Paid Plugin - License - NativePHP</title>', false)
            ->assertSee('<meta property="og:title" content="Paid Plugin">', false);
    }
}
