<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ReviewPluginRepository;
use App\Models\Plugin;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReviewPluginRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, PromiseInterface>
     */
    protected function fakeGitHub(string $repoSlug, array $tree = [], array $composerRequire = [], ?array $nativephpJson = null, string $defaultBranch = 'main', ?string $latestRelease = null): array
    {
        $base = "https://api.github.com/repos/{$repoSlug}";

        $fakes = [
            $base => Http::response(['default_branch' => $defaultBranch]),
            "{$base}/git/trees/{$defaultBranch}*" => Http::response(['tree' => $tree]),
            "{$base}/contents/composer.json" => Http::response([
                'content' => base64_encode(json_encode(['require' => $composerRequire])),
                'encoding' => 'base64',
            ]),
        ];

        if ($nativephpJson !== null) {
            $fakes["{$base}/contents/nativephp.json"] = Http::response([
                'content' => base64_encode(json_encode($nativephpJson)),
                'encoding' => 'base64',
            ]);
        } else {
            $fakes["{$base}/contents/nativephp.json"] = Http::response([], 404);
        }

        if ($latestRelease !== null) {
            $fakes["{$base}/releases/latest"] = Http::response(['tag_name' => $latestRelease]);
        } else {
            $fakes["{$base}/releases/latest"] = Http::response([], 404);
            $fakes["{$base}/tags*"] = Http::response([]);
        }

        return $fakes;
    }

    /** @test */
    public function it_detects_ios_android_and_js_support_from_repo_tree(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/test-plugin', tree: [
            ['path' => 'resources/ios/Plugin.swift', 'type' => 'blob'],
            ['path' => 'resources/android/Plugin.kt', 'type' => 'blob'],
            ['path' => 'resources/js/index.js', 'type' => 'blob'],
            ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['supports_ios']);
        $this->assertTrue($checks['supports_android']);
        $this->assertTrue($checks['supports_js']);
    }

    /** @test */
    public function it_reports_missing_platform_directories(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/bare-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/bare-plugin', tree: [
            ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['supports_ios']);
        $this->assertFalse($checks['supports_android']);
        $this->assertFalse($checks['supports_js']);
    }

    /** @test */
    public function it_only_counts_blobs_not_trees_for_directory_check(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/tree-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/tree-plugin', tree: [
            ['path' => 'resources/ios', 'type' => 'tree'],
            ['path' => 'resources/android', 'type' => 'tree'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['supports_ios']);
        $this->assertFalse($checks['supports_android']);
    }

    /** @test */
    public function it_detects_nativephp_mobile_dependency(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/sdk-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/sdk-plugin', composerRequire: [
            'php' => '^8.1',
            'nativephp/mobile' => '^3.0.0',
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['requires_mobile_sdk']);
        $this->assertEquals('^3.0.0', $checks['mobile_sdk_constraint']);
    }

    /** @test */
    public function it_reports_missing_mobile_sdk_dependency(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/no-sdk-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/no-sdk-plugin', composerRequire: ['php' => '^8.1']));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['requires_mobile_sdk']);
        $this->assertNull($checks['mobile_sdk_constraint']);
    }

    /** @test */
    public function it_detects_ios_and_android_min_versions_from_nativephp_json(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/versioned-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/versioned-plugin', nativephpJson: [
            'ios' => ['min_version' => '16.0'],
            'android' => ['min_version' => '24'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['has_ios_min_version']);
        $this->assertEquals('16.0', $checks['ios_min_version']);
        $this->assertTrue($checks['has_android_min_version']);
        $this->assertEquals('24', $checks['android_min_version']);
    }

    /** @test */
    public function it_reports_missing_min_versions_when_no_nativephp_json(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/no-manifest-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/no-manifest-plugin'));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['has_ios_min_version']);
        $this->assertNull($checks['ios_min_version']);
        $this->assertFalse($checks['has_android_min_version']);
        $this->assertNull($checks['android_min_version']);
    }

    /** @test */
    public function it_reports_missing_min_version_when_nativephp_json_has_partial_data(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/partial-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/partial-plugin', nativephpJson: [
            'ios' => ['min_version' => '15.0'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['has_ios_min_version']);
        $this->assertEquals('15.0', $checks['ios_min_version']);
        $this->assertFalse($checks['has_android_min_version']);
        $this->assertNull($checks['android_min_version']);
    }

    /** @test */
    public function it_stores_results_in_review_checks_and_stamps_reviewed_at(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/store-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/store-plugin',
            tree: [['path' => 'resources/ios/Bridge.swift', 'type' => 'blob']],
            composerRequire: ['nativephp/mobile' => '^3.0.0'],
        ));

        $this->assertNull($plugin->reviewed_at);
        $this->assertNull($plugin->review_checks);

        (new ReviewPluginRepository($plugin))->handle();

        $plugin->refresh();

        $this->assertNotNull($plugin->reviewed_at);
        $this->assertIsArray($plugin->review_checks);
        $this->assertTrue($plugin->review_checks['supports_ios']);
        $this->assertFalse($plugin->review_checks['supports_android']);
        $this->assertTrue($plugin->review_checks['requires_mobile_sdk']);
    }

    /** @test */
    public function it_returns_empty_array_when_no_repository_url(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => null,
        ]);

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertEmpty($checks);
        $this->assertNull($plugin->fresh()->reviewed_at);
    }

    /** @test */
    public function it_uses_the_repos_default_branch_not_hardcoded_main(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/master-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/master-plugin',
            tree: [
                ['path' => 'resources/ios/Plugin.swift', 'type' => 'blob'],
                ['path' => 'resources/android/Plugin.kt', 'type' => 'blob'],
            ],
            defaultBranch: 'master',
        ));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['supports_ios']);
        $this->assertTrue($checks['supports_android']);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/git/trees/master'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/git/trees/main'));
    }

    /** @test */
    public function it_detects_license_file_in_repo(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/licensed-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/licensed-plugin', tree: [
            ['path' => 'LICENSE', 'type' => 'blob'],
            ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['has_license_file']);
    }

    /** @test */
    public function it_detects_license_md_file_in_repo(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/licensed-md-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/licensed-md-plugin', tree: [
            ['path' => 'LICENSE.md', 'type' => 'blob'],
            ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['has_license_file']);
    }

    /** @test */
    public function it_reports_missing_license_file(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/unlicensed-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/unlicensed-plugin', tree: [
            ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['has_license_file']);
    }

    /** @test */
    public function it_does_not_count_license_directory_as_license_file(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/license-dir-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/license-dir-plugin', tree: [
            ['path' => 'LICENSE', 'type' => 'tree'],
        ]));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['has_license_file']);
    }

    /** @test */
    public function it_detects_release_version_from_github_release(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/released-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/released-plugin', latestRelease: 'v1.0.0'));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['has_release_version']);
        $this->assertEquals('v1.0.0', $checks['release_version']);
    }

    /** @test */
    public function it_falls_back_to_tags_when_no_release_exists(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/tagged-plugin',
        ]);

        $base = 'https://api.github.com/repos/acme/tagged-plugin';

        Http::fake(array_merge(
            $this->fakeGitHub('acme/tagged-plugin'),
            [
                "{$base}/releases/latest" => Http::response([], 404),
                "{$base}/tags*" => Http::response([
                    ['name' => 'v0.5.0'],
                ]),
            ]
        ));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertTrue($checks['has_release_version']);
        $this->assertEquals('v0.5.0', $checks['release_version']);
    }

    /** @test */
    public function it_reports_missing_release_version(): void
    {
        $plugin = Plugin::factory()->create([
            'repository_url' => 'https://github.com/acme/unreleased-plugin',
        ]);

        Http::fake($this->fakeGitHub('acme/unreleased-plugin'));

        $checks = (new ReviewPluginRepository($plugin))->handle();

        $this->assertFalse($checks['has_release_version']);
        $this->assertNull($checks['release_version']);
    }
}
