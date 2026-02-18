<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerPluginReviewChecksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function submitting_a_plugin_runs_review_checks(): void
    {
        $user = User::factory()->create([
            'github_id' => '12345',
        ]);

        $repoSlug = 'acme/test-plugin';
        $base = "https://api.github.com/repos/{$repoSlug}";
        $composerJson = json_encode([
            'name' => 'acme/test-plugin',
            'description' => 'A test plugin',
            'require' => [
                'php' => '^8.1',
                'nativephp/mobile' => '^3.0.0',
            ],
        ]);

        Http::fake([
            // PluginSyncService calls
            "{$base}/contents/README.md" => Http::response([
                'content' => base64_encode("# Test Plugin\n\nSupport: dev@testplugin.io"),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/composer.json" => Http::response([
                'content' => base64_encode($composerJson),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/nativephp.json" => Http::response([], 404),
            "{$base}/contents/LICENSE*" => Http::response([], 404),
            "{$base}/releases/latest" => Http::response([], 404),
            "{$base}/tags*" => Http::response([]),
            "https://raw.githubusercontent.com/{$repoSlug}/*" => Http::response('', 404),

            // ReviewPluginRepository calls
            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'resources/ios/Plugin.swift', 'type' => 'blob'],
                    ['path' => 'resources/android/Plugin.kt', 'type' => 'blob'],
                    ['path' => 'resources/js/index.js', 'type' => 'blob'],
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                ],
            ]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode("# Test Plugin\n\nSupport: dev@testplugin.io"),
                'encoding' => 'base64',
            ]),
        ]);

        $response = $this->actingAs($user)
            ->post(route('customer.plugins.store'), [
                'repository' => $repoSlug,
                'type' => 'free',
            ]);

        $response->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        $this->assertNotNull($plugin, 'Plugin should exist after submission');
        $this->assertNotNull($plugin->review_checks, 'review_checks should be populated');
        $this->assertTrue($plugin->review_checks['supports_ios']);
        $this->assertTrue($plugin->review_checks['supports_android']);
        $this->assertTrue($plugin->review_checks['supports_js']);
        $this->assertTrue($plugin->review_checks['has_support_email']);
        $this->assertEquals('dev@testplugin.io', $plugin->review_checks['support_email']);
        $this->assertTrue($plugin->review_checks['requires_mobile_sdk']);
        $this->assertEquals('^3.0.0', $plugin->review_checks['mobile_sdk_constraint']);
        $this->assertNotNull($plugin->reviewed_at);
    }
}
