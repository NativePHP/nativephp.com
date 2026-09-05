<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginPermissionAndChangelogDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_it_shows_declared_permissions_from_the_latest_visible_version(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => now(),
            'manifest_permissions' => [
                'android_permissions' => ['android.permission.CAMERA'],
                'ios_background_modes' => ['fetch'],
            ],
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertSee('What This Plugin Can Access');
        $response->assertSee('Camera');
        $response->assertSee('Background Fetch');
    }

    public function test_it_does_not_show_the_permission_panel_when_nothing_is_declared(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => now(),
            'manifest_permissions' => ['android_permissions' => []],
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertDontSee('What This Plugin Can Access');
    }

    public function test_a_version_pending_review_is_not_used_for_the_permission_panel(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => now(),
            'manifest_permissions' => ['android_permissions' => ['android.permission.CAMERA']],
        ]);

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '2.0.0',
            'tag_name' => 'v2.0.0',
            'github_release_id' => '2',
            'published_at' => now()->addDay(),
            'manifest_permissions' => ['android_permissions' => ['android.permission.CAMERA', 'android.permission.ACCESS_FINE_LOCATION']],
            'requires_review' => true,
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertSee('Camera');
        $response->assertDontSee('Precise Location');
    }

    public function test_it_shows_version_history_with_rendered_release_notes(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => now(),
            'release_notes' => '**Fixed** a crash on launch.',
            'release_notes_html' => '<p><strong>Fixed</strong> a crash on launch.</p>',
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertSee('Version History');
        $response->assertSee('<strong>Fixed</strong> a crash on launch.', false);
    }

    public function test_version_history_excludes_versions_pending_review(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '2.0.0',
            'tag_name' => 'v2.0.0',
            'github_release_id' => '2',
            'published_at' => now(),
            'release_notes_html' => '<p>Secret unreleased notes.</p>',
            'requires_review' => true,
        ]);

        $response = $this->get(route('plugins.show', $plugin->routeParams()));

        $response->assertStatus(200);
        $response->assertDontSee('Secret unreleased notes.');
    }
}
