<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Services\PluginSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PluginSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function test_sync_extracts_mobile_min_version_from_composer_data(): void
    {
        $composerJson = json_encode([
            'name' => 'acme/test-plugin',
            'require' => [
                'nativephp/mobile' => '^3.0.0',
            ],
        ]);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode($composerJson),
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
            'api.github.com/repos/acme/test-plugin/releases/latest' => Http::response([], 404),
            'api.github.com/repos/acme/test-plugin/tags*' => Http::response([]),
            'api.github.com/repos/acme/test-plugin/contents/LICENSE*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'mobile_min_version' => null,
        ]);

        $service = new PluginSyncService;
        $result = $service->sync($plugin);

        $this->assertTrue($result);
        $this->assertEquals('^3.0.0', $plugin->fresh()->mobile_min_version);
    }

    public function test_sync_updates_name_from_composer_when_name_changes(): void
    {
        $composerJson = json_encode([
            'name' => 'acme/renamed-plugin',
        ]);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode($composerJson),
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
            'api.github.com/repos/acme/test-plugin/releases/latest' => Http::response([], 404),
            'api.github.com/repos/acme/test-plugin/tags*' => Http::response([]),
            'api.github.com/repos/acme/test-plugin/contents/LICENSE*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/old-name',
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        $service = new PluginSyncService;
        $result = $service->sync($plugin);

        $this->assertTrue($result);
        $this->assertEquals('acme/renamed-plugin', $plugin->fresh()->name);
    }

    public function test_sync_sets_name_on_initial_sync_when_plugin_has_no_name(): void
    {
        $composerJson = json_encode([
            'name' => 'acme/test-plugin',
        ]);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode($composerJson),
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
            'api.github.com/repos/acme/test-plugin/releases/latest' => Http::response([], 404),
            'api.github.com/repos/acme/test-plugin/tags*' => Http::response([]),
            'api.github.com/repos/acme/test-plugin/contents/LICENSE*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => null,
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        $service = new PluginSyncService;
        $result = $service->sync($plugin);

        $this->assertTrue($result);
        $this->assertEquals('acme/test-plugin', $plugin->fresh()->name);
    }

    public function test_sync_does_not_overwrite_name_when_taken_by_another_plugin(): void
    {
        $composerJson = json_encode([
            'name' => 'acme/taken-name',
        ]);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode($composerJson),
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
            'api.github.com/repos/acme/test-plugin/releases/latest' => Http::response([], 404),
            'api.github.com/repos/acme/test-plugin/tags*' => Http::response([]),
            'api.github.com/repos/acme/test-plugin/contents/LICENSE*' => Http::response([], 404),
        ]);

        Plugin::factory()->create(['name' => 'acme/taken-name']);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/original-name',
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        $service = new PluginSyncService;
        $result = $service->sync($plugin);

        $this->assertTrue($result);
        $this->assertEquals('acme/original-name', $plugin->fresh()->name);
    }

    public function test_sync_sets_mobile_min_version_to_null_when_not_in_composer_data(): void
    {
        $composerJson = json_encode([
            'name' => 'acme/test-plugin',
            'require' => [
                'php' => '^8.2',
            ],
        ]);

        Http::fake([
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode($composerJson),
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
            'api.github.com/repos/acme/test-plugin/releases/latest' => Http::response([], 404),
            'api.github.com/repos/acme/test-plugin/tags*' => Http::response([]),
            'api.github.com/repos/acme/test-plugin/contents/LICENSE*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'mobile_min_version' => '^2.0.0',
        ]);

        $service = new PluginSyncService;
        $result = $service->sync($plugin);

        $this->assertTrue($result);
        $this->assertNull($plugin->fresh()->mobile_min_version);
    }
}
