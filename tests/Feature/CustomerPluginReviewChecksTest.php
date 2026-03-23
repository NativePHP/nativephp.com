<?php

namespace Tests\Feature;

use App\Livewire\Customer\Plugins\Create;
use App\Models\DeveloperAccount;
use App\Models\User;
use App\Notifications\PluginSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerPluginReviewChecksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function submitting_a_plugin_runs_review_checks(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'github_id' => '12345',
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
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

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->call('submitPlugin')
            ->assertRedirect();

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

        Notification::assertSentTo($user, PluginSubmitted::class, function (PluginSubmitted $notification) use ($plugin) {
            return $notification->plugin->id === $plugin->id;
        });
    }

    /** @test */
    public function plugin_submitted_email_includes_failing_checks(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'github_id' => '12345',
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        $repoSlug = 'acme/bare-plugin';
        $base = "https://api.github.com/repos/{$repoSlug}";
        $composerJson = json_encode([
            'name' => 'acme/bare-plugin',
            'description' => 'A bare plugin',
            'require' => ['php' => '^8.1'],
        ]);

        Http::fake([
            "{$base}/contents/README.md" => Http::response([
                'content' => base64_encode('# Bare Plugin'),
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

            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                ],
            ]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode('# Bare Plugin'),
                'encoding' => 'base64',
            ]),
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->call('submitPlugin');

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        Notification::assertSentTo($user, PluginSubmitted::class, function (PluginSubmitted $notification) use ($plugin) {
            $mail = $notification->toMail($plugin->user);
            $rendered = $mail->render()->toHtml();

            // Should mention failing checks
            $this->assertStringContainsString('Add iOS support', $rendered);
            $this->assertStringContainsString('Add Android support', $rendered);
            $this->assertStringContainsString('Add JavaScript support', $rendered);
            $this->assertStringContainsString('Add a support email', $rendered);
            $this->assertStringContainsString('nativephp/mobile SDK', $rendered);

            return true;
        });
    }
}
