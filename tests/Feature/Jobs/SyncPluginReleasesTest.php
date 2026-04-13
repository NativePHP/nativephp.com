<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SyncPluginReleases;
use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Services\SatisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncPluginReleasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_latest_version_on_plugin_when_new_releases_are_synced(): void
    {
        Http::fake([
            'api.github.com/repos/acme/test-plugin/releases*' => Http::response([
                [
                    'id' => 1,
                    'tag_name' => 'v1.0.0',
                    'body' => 'Initial release',
                    'target_commitish' => 'abc123',
                    'published_at' => '2026-01-01T00:00:00Z',
                ],
                [
                    'id' => 2,
                    'tag_name' => 'v1.1.0',
                    'body' => 'New features',
                    'target_commitish' => 'def456',
                    'published_at' => '2026-02-01T00:00:00Z',
                ],
            ]),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'latest_version' => '0.9.0',
        ]);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle(app(SatisService::class));

        $plugin->refresh();

        $this->assertEquals('1.1.0', $plugin->latest_version);
        $this->assertCount(2, $plugin->versions);
    }

    public function test_it_does_not_update_latest_version_when_no_new_releases(): void
    {
        Http::fake([
            'api.github.com/repos/acme/test-plugin/releases*' => Http::response([
                [
                    'id' => 1,
                    'tag_name' => 'v1.0.0',
                    'body' => 'Initial release',
                    'target_commitish' => 'abc123',
                    'published_at' => '2026-01-01T00:00:00Z',
                ],
            ]),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'latest_version' => '1.0.0',
        ]);

        // Pre-create the version so nothing is "new"
        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => '2026-01-01T00:00:00Z',
        ]);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle(app(SatisService::class));

        $plugin->refresh();

        $this->assertEquals('1.0.0', $plugin->latest_version);
    }
}
