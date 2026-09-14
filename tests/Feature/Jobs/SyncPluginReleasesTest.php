<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SyncPluginReleases;
use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Services\PluginManifestParser;
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

        $satisService = $this->mock(SatisService::class);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle($satisService, new PluginManifestParser);

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

        $satisService = $this->mock(SatisService::class);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle($satisService, new PluginManifestParser);

        $plugin->refresh();

        $this->assertEquals('1.0.0', $plugin->latest_version);
    }

    public function test_it_stores_manifest_permissions_from_the_release_tags_nativephp_json(): void
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
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json*' => Http::response([
                'content' => base64_encode(json_encode([
                    'android' => ['permissions' => ['android.permission.CAMERA']],
                ])),
                'encoding' => 'base64',
            ]),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        $satisService = $this->mock(SatisService::class);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle($satisService, new PluginManifestParser);

        $version = $plugin->versions()->first();

        $this->assertSame(['android.permission.CAMERA'], $version->manifest_permissions['android_permissions']);
        $this->assertFalse($version->permissions_expanded);
        $this->assertFalse($version->requires_review);
    }

    public function test_it_flags_permission_expansion_and_still_publishes_in_flag_mode(): void
    {
        config(['plugins.permission_expansion_mode' => 'flag']);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/releases*' => Http::response([
                [
                    'id' => 2,
                    'tag_name' => 'v2.0.0',
                    'body' => 'Adds camera support',
                    'target_commitish' => 'def456',
                    'published_at' => '2026-02-01T00:00:00Z',
                ],
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json*' => Http::response([
                'content' => base64_encode(json_encode([
                    'android' => ['permissions' => ['android.permission.CAMERA']],
                ])),
                'encoding' => 'base64',
            ]),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => '2026-01-01T00:00:00Z',
            'manifest_permissions' => ['android_permissions' => []],
        ]);

        $satisService = $this->mock(SatisService::class);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle($satisService, new PluginManifestParser);

        $plugin->refresh();
        $newVersion = $plugin->versions()->where('tag_name', 'v2.0.0')->first();

        $this->assertTrue($newVersion->permissions_expanded);
        $this->assertFalse($newVersion->requires_review);
        $this->assertEquals('2.0.0', $plugin->latest_version);
        $this->assertDatabaseHas('plugin_activities', [
            'plugin_id' => $plugin->id,
            'type' => 'permissions_expanded',
        ]);
    }

    public function test_it_gates_expanded_permissions_and_withholds_latest_version_in_gate_mode(): void
    {
        config(['plugins.permission_expansion_mode' => 'gate']);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/releases*' => Http::response([
                [
                    'id' => 2,
                    'tag_name' => 'v2.0.0',
                    'body' => 'Adds camera support',
                    'target_commitish' => 'def456',
                    'published_at' => '2026-02-01T00:00:00Z',
                ],
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json*' => Http::response([
                'content' => base64_encode(json_encode([
                    'android' => ['permissions' => ['android.permission.CAMERA']],
                ])),
                'encoding' => 'base64',
            ]),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'latest_version' => '1.0.0',
        ]);

        PluginVersion::create([
            'plugin_id' => $plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => '2026-01-01T00:00:00Z',
            'manifest_permissions' => ['android_permissions' => []],
        ]);

        $satisService = $this->mock(SatisService::class);

        $job = new SyncPluginReleases($plugin, triggerSatisBuild: false);
        $job->handle($satisService, new PluginManifestParser);

        $plugin->refresh();
        $newVersion = $plugin->versions()->where('tag_name', 'v2.0.0')->first();

        $this->assertTrue($newVersion->requires_review);
        $this->assertEquals('1.0.0', $plugin->latest_version);
    }
}
