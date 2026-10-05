<?php

namespace Tests\Feature;

use App\Models\Plugin;
use App\Models\User;
use App\Services\PluginSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    public function test_sync_reads_all_listing_content_at_the_release_commit(): void
    {
        $this->assertSyncUsesReleaseCommit(true);
    }

    public function test_sync_uses_a_tag_commit_when_no_release_exists(): void
    {
        $this->assertSyncUsesReleaseCommit(false);
    }

    private function assertSyncUsesReleaseCommit(bool $hasRelease): void
    {
        $base = 'https://api.github.com/repos/acme/test-plugin';
        $commitSha = str_repeat('a', 40);
        $tag = 'release/v1.2.3';
        $files = [
            'README.md' => '# Released documentation',
            'composer.json' => json_encode(['name' => 'acme/test-plugin', 'description' => 'Released description', 'require' => ['nativephp/mobile' => '^3.0']]),
            'nativephp.json' => json_encode(['ios' => ['min_version' => '16.0'], 'android' => ['min_version' => '28']]),
            'LICENSE.md' => 'Released license',
        ];

        Http::fake([
            "{$base}/releases/latest" => Http::response($hasRelease ? ['tag_name' => $tag, 'target_commitish' => 'main'] : [], $hasRelease ? 200 : 404),
            "{$base}/tags*" => Http::response([['name' => $tag]]),
            "{$base}/commits/".rawurlencode($tag) => Http::response(['sha' => $commitSha]),
            "{$base}/contents/*" => function (Request $request) use ($files, $commitSha) {
                $this->assertSame($commitSha, $request['ref']);
                $path = basename(parse_url($request->url(), PHP_URL_PATH));

                return Http::response(['content' => base64_encode($files[$path])]);
            },
            '*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create(['name' => 'acme/test-plugin', 'repository_url' => 'https://github.com/acme/test-plugin']);

        $this->assertTrue((new PluginSyncService)->sync($plugin));
        $plugin->refresh();
        $this->assertStringContainsString('Released documentation', $plugin->readme_html);
        $this->assertStringContainsString('Released license', $plugin->license_html);
        $this->assertSame('Released description', $plugin->description);
        $this->assertSame('16.0', $plugin->ios_version);
        $this->assertSame('28', $plugin->android_version);
        $this->assertSame('^3.0', $plugin->mobile_min_version);
        $this->assertSame('release/v1.2.3', $plugin->latest_version);
    }

    public function test_raw_fallback_uses_the_release_commit_and_never_reads_main(): void
    {
        $base = 'https://api.github.com/repos/acme/test-plugin';
        $commitSha = str_repeat('b', 40);

        Http::fake([
            "{$base}/releases/latest" => Http::response(['tag_name' => 'v1.2.3']),
            "{$base}/commits/v1.2.3" => Http::response(['sha' => $commitSha]),
            "https://raw.githubusercontent.com/acme/test-plugin/{$commitSha}/composer.json" => Http::response(json_encode(['name' => 'acme/test-plugin'])),
            "https://raw.githubusercontent.com/acme/test-plugin/{$commitSha}/README.md" => Http::response('# Released fallback'),
            '*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'license_html' => 'Existing license',
        ]);

        $this->assertTrue((new PluginSyncService)->sync($plugin));
        $this->assertSame('1.2.3', $plugin->fresh()->latest_version);
        $this->assertStringContainsString('Released fallback', $plugin->fresh()->readme_html);
        $this->assertSame('Existing license', $plugin->fresh()->license_html);
        Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/main/'));
    }

    public function test_sync_leaves_listing_unchanged_when_release_commit_cannot_be_resolved(): void
    {
        Http::fake([
            '*/releases/latest' => Http::response(['tag_name' => 'v2.0.0']),
            '*' => Http::response([], 404),
        ]);
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/test-plugin',
            'readme_html' => 'Existing documentation',
            'latest_version' => '1.0.0',
            'last_synced_at' => null,
        ]);

        $this->assertFalse((new PluginSyncService)->sync($plugin));
        $plugin->refresh();
        $this->assertSame('Existing documentation', $plugin->readme_html);
        $this->assertSame('1.0.0', $plugin->latest_version);
        $this->assertNull($plugin->last_synced_at);
        Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/contents/') || str_contains($request->url(), 'raw.githubusercontent.com'));
        Queue::assertNothingPushed();
    }

    public function test_sync_does_not_read_default_branch_when_release_discovery_fails(): void
    {
        Log::spy();

        Http::fake(['*' => Http::response([], 403)]);
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/test-plugin',
            'readme_html' => 'Existing documentation',
            'last_synced_at' => null,
        ]);

        $this->assertFalse((new PluginSyncService)->sync($plugin));
        Log::shouldHaveReceived('warning')->once()->with(
            '[PluginSync] Could not discover latest release',
            \Mockery::on(fn (array $context): bool => $context['plugin_id'] === $plugin->id
                && str_contains($context['error'], '403')),
        );
        $this->assertSame('Existing documentation', $plugin->fresh()->readme_html);
        $this->assertNull($plugin->fresh()->last_synced_at);
        Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/contents/'));
    }

    public function test_sync_reads_default_branch_when_repository_has_no_tags_or_releases(): void
    {
        Http::fake([
            '*/releases/latest' => Http::response([], 404),
            '*/tags*' => Http::response([]),
            '*/contents/README.md' => Http::response(['content' => base64_encode('# Unreleased documentation')]),
            '*/contents/composer.json' => Http::response(['content' => base64_encode(json_encode(['name' => 'acme/test-plugin']))]),
            '*' => Http::response([], 404),
        ]);
        $plugin = Plugin::factory()->create(['repository_url' => 'https://github.com/acme/test-plugin']);

        $this->assertTrue((new PluginSyncService)->sync($plugin));
        $this->assertStringContainsString('Unreleased documentation', $plugin->fresh()->readme_html);
        Http::assertNotSent(fn (Request $request): bool => isset($request['ref']) || str_contains($request->url(), '/commits/'));
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

    public function test_sync_works_out_supported_mobile_versions_from_composer_data(): void
    {
        $composerJson = json_encode([
            'name' => 'acme/test-plugin',
            'require' => [
                'nativephp/mobile' => '^3.2.1 || ^4.0',
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
            'mobile_versions' => null,
        ]);

        $this->assertTrue((new PluginSyncService)->sync($plugin));
        $this->assertSame([3 => '3.2.1', 4 => '4.0'], $plugin->fresh()->mobile_versions);
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

    public function test_sync_skips_an_owner_token_github_has_revoked(): void
    {
        config(['services.github.token' => 'ghp_platform']);

        Http::fake([
            'api.github.com/repos/acme/test-plugin' => Http::response(['message' => 'Bad credentials'], 401),
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode(json_encode(['name' => 'acme/test-plugin'])),
            ]),
            'api.github.com/*' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
        ]);

        $plugin = Plugin::factory()
            ->for(User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_revoked')]))
            ->create([
                'name' => 'acme/test-plugin',
                'repository_url' => 'https://github.com/acme/test-plugin',
            ]);

        $this->assertTrue((new PluginSyncService)->sync($plugin));

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/contents/composer.json')
            && $request->hasHeader('Authorization', 'Bearer ghp_platform'));
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

        $plugin = Plugin::factory()->mobileVersions('3.0')->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
            'mobile_min_version' => '^3.0.0',
        ]);

        $service = new PluginSyncService;
        $result = $service->sync($plugin);

        $this->assertTrue($result);
        $this->assertNull($plugin->fresh()->mobile_min_version);
        $this->assertNull($plugin->fresh()->mobile_versions);
    }
}
