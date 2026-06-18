<?php

namespace Tests\Feature;

use App\Livewire\Customer\Plugins\Create;
use App\Livewire\Customer\Plugins\Show;
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

    private function fakeGitHubForCreateAndSubmit(string $repoSlug): void
    {
        $base = "https://api.github.com/repos/{$repoSlug}";
        $composerJson = json_encode([
            'name' => $repoSlug,
            'description' => 'A test plugin',
            'require' => [
                'php' => '^8.1',
                'nativephp/mobile' => '^3.0.0',
            ],
        ]);

        Http::fake([
            // PluginSyncService calls
            "{$base}/contents/README.md" => Http::response([
                'content' => base64_encode('# Test Plugin'),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/composer.json*" => Http::response([
                'content' => base64_encode($composerJson),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/nativephp.json" => Http::response([], 404),
            "{$base}/contents/LICENSE*" => Http::response([], 404),
            "{$base}/releases/latest" => Http::response(['tag_name' => 'v1.0.0']),
            "{$base}/tags*" => Http::response([]),
            "https://raw.githubusercontent.com/{$repoSlug}/*" => Http::response('', 404),

            // Webhook creation
            "{$base}/hooks" => function ($request) {
                if ($request->method() === 'GET') {
                    return Http::response([], 200);
                }

                return Http::response(['id' => 1], 201);
            },

            // ReviewPluginRepository calls
            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'LICENSE', 'type' => 'blob'],
                    ['path' => 'resources/ios/Plugin.swift', 'type' => 'blob'],
                    ['path' => 'resources/android/Plugin.kt', 'type' => 'blob'],
                    ['path' => 'resources/js/index.js', 'type' => 'blob'],
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                ],
            ]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode('# Test Plugin'),
                'encoding' => 'base64',
            ]),
        ]);
    }

    /** @test */
    public function submitting_a_plugin_for_review_runs_review_checks(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'github_id' => '12345',
            'github_token' => encrypt('fake-token'),
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        $repoSlug = 'acme/test-plugin';
        $this->fakeGitHubForCreateAndSubmit($repoSlug);

        // Step 1: Create the draft
        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->call('createPlugin')
            ->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();
        $this->assertNotNull($plugin, 'Plugin should exist after creation');
        $this->assertEquals('draft', $plugin->status->value);

        // Set support channel (required before submission)
        $plugin->update(['support_channel' => 'dev@testplugin.io']);

        // Re-fake HTTP for the submission step
        $this->fakeGitHubForCreateAndSubmit($repoSlug);

        // Step 2: Submit for review from the Show page
        [$vendor, $package] = explode('/', $plugin->name);
        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->call('submitForReview');

        $plugin->refresh();

        $this->assertEquals('pending', $plugin->status->value);
        $this->assertNotNull($plugin->review_checks, 'review_checks should be populated');
        $this->assertTrue($plugin->review_checks['has_license_file']);
        $this->assertTrue($plugin->review_checks['has_release_version']);
        $this->assertEquals('v1.0.0', $plugin->review_checks['release_version']);
        $this->assertTrue($plugin->review_checks['supports_ios']);
        $this->assertTrue($plugin->review_checks['supports_android']);
        $this->assertTrue($plugin->review_checks['supports_js']);
        $this->assertTrue($plugin->review_checks['requires_mobile_sdk']);
        $this->assertEquals('^3.0.0', $plugin->review_checks['mobile_sdk_constraint']);
        $this->assertNotNull($plugin->reviewed_at);

        Notification::assertSentTo($user, PluginSubmitted::class, function (PluginSubmitted $notification) use ($plugin) {
            return $notification->plugin->id === $plugin->id;
        });
    }

    /** @test */
    public function plugin_submitted_email_includes_failing_optional_checks(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'github_id' => '12345',
            'github_token' => encrypt('fake-token'),
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
            "{$base}/contents/composer.json*" => Http::response([
                'content' => base64_encode($composerJson),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/nativephp.json" => Http::response([], 404),
            "{$base}/contents/LICENSE*" => Http::response([], 404),
            "{$base}/releases/latest" => Http::response(['tag_name' => 'v1.0.0']),
            "{$base}/tags*" => Http::response([['name' => 'v1.0.0']]),
            "https://raw.githubusercontent.com/{$repoSlug}/*" => Http::response('', 404),
            "{$base}/hooks" => function ($request) {
                if ($request->method() === 'GET') {
                    return Http::response([], 200);
                }

                return Http::response(['id' => 1], 201);
            },
            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                    ['path' => 'LICENSE', 'type' => 'blob'],
                ],
            ]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode('# Bare Plugin'),
                'encoding' => 'base64',
            ]),
        ]);

        // Step 1: Create the draft
        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->call('createPlugin');

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        // Set support channel (required before submission)
        $plugin->update(['support_channel' => 'support@bare-plugin.io']);

        // Re-fake HTTP for submission
        Http::fake([
            "{$base}/contents/composer.json*" => Http::response([
                'content' => base64_encode($composerJson),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/README.md" => Http::response([
                'content' => base64_encode('# Bare Plugin'),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/nativephp.json" => Http::response([], 404),
            "{$base}/contents/LICENSE*" => Http::response([], 404),
            "{$base}/releases/latest" => Http::response(['tag_name' => 'v1.0.0']),
            "{$base}/tags*" => Http::response([['name' => 'v1.0.0']]),
            "{$base}/hooks" => function ($request) {
                if ($request->method() === 'GET') {
                    return Http::response([], 200);
                }

                return Http::response(['id' => 1], 201);
            },
            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                    ['path' => 'LICENSE', 'type' => 'blob'],
                ],
            ]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode('# Bare Plugin'),
                'encoding' => 'base64',
            ]),
            "https://raw.githubusercontent.com/{$repoSlug}/*" => Http::response('', 404),
        ]);

        // Step 2: Submit for review (required checks pass, optional ones don't)
        [$vendor, $package] = explode('/', $plugin->name);
        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->call('submitForReview');

        $plugin->refresh();

        $this->assertEquals('pending', $plugin->status->value);

        Notification::assertSentTo($user, PluginSubmitted::class, function (PluginSubmitted $notification) use ($plugin) {
            $mail = $notification->toMail($plugin->user);
            $rendered = $mail->render()->toHtml();

            // Should mention failing optional checks
            $this->assertStringContainsString('Add iOS support', $rendered);
            $this->assertStringContainsString('Add Android support', $rendered);
            $this->assertStringContainsString('Add JavaScript support', $rendered);
            $this->assertStringContainsString('nativephp/mobile SDK', $rendered);

            return true;
        });
    }
}
