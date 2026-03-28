<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginTableOfContentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_toc_component_rendered_when_readme_has_content(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'readme_html' => '<h2 id="installation">Installation</h2><p>Steps here.</p><h2 id="usage">Usage</h2><p>More content.</p>',
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('On this page');
    }

    public function test_toc_component_not_rendered_when_no_readme(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'readme_html' => null,
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('On this page');
    }
}
