<?php

namespace Tests\Feature;

use App\Livewire\Customer\Plugins\Create;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class PluginSubmissionNotesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $extraComposerData
     */
    private function fakeGitHubForPlugin(string $repoSlug, array $extraComposerData = []): void
    {
        $base = "https://api.github.com/repos/{$repoSlug}";
        $composerJson = json_encode(array_merge([
            'name' => $repoSlug,
            'description' => "A test plugin: {$repoSlug}",
            'require' => [
                'php' => '^8.1',
                'nativephp/mobile' => '^3.0.0',
            ],
        ], $extraComposerData));

        Http::fake([
            "{$base}/contents/README.md*" => Http::response([
                'content' => base64_encode("# {$repoSlug}"),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/composer.json*" => Http::response([
                'content' => base64_encode($composerJson),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/nativephp.json*" => Http::response([], 404),
            "{$base}/contents/LICENSE*" => Http::response([], 404),
            "{$base}/releases/latest" => Http::response([], 404),
            "{$base}/tags*" => Http::response([]),
            "{$base}/hooks" => Http::response(['id' => 1]),
            "https://raw.githubusercontent.com/{$repoSlug}/*" => Http::response('', 404),
            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                ],
            ]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode("# {$repoSlug}"),
                'encoding' => 'base64',
            ]),
        ]);
    }

    private function createUserWithGitHub(): User
    {
        $user = User::factory()->create([
            'github_id' => '12345',
            'github_token' => encrypt('fake-token'),
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /** @test */
    public function submitting_a_plugin_saves_notes(): void
    {
        Notification::fake();
        $user = $this->createUserWithGitHub();
        $repoSlug = 'acme/notes-plugin';
        $this->fakeGitHubForPlugin($repoSlug);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->set('notes', 'Please review this quickly, we have a launch deadline.')
            ->call('submitPlugin')
            ->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        $this->assertNotNull($plugin);
        $this->assertEquals('Please review this quickly, we have a launch deadline.', $plugin->notes);
    }

    /** @test */
    public function submitting_a_plugin_without_notes_stores_null(): void
    {
        Notification::fake();
        $user = $this->createUserWithGitHub();
        $repoSlug = 'acme/no-notes-plugin';
        $this->fakeGitHubForPlugin($repoSlug);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->call('submitPlugin')
            ->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        $this->assertNotNull($plugin);
        $this->assertNull($plugin->notes);
    }

    /** @test */
    public function submitting_a_plugin_saves_support_channel_email(): void
    {
        Notification::fake();
        $user = $this->createUserWithGitHub();
        $repoSlug = 'acme/support-email-plugin';
        $this->fakeGitHubForPlugin($repoSlug);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->set('supportChannel', 'help@example.com')
            ->call('submitPlugin')
            ->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        $this->assertNotNull($plugin);
        $this->assertEquals('help@example.com', $plugin->support_channel);
    }

    /** @test */
    public function submitting_a_plugin_saves_support_channel_url(): void
    {
        Notification::fake();
        $user = $this->createUserWithGitHub();
        $repoSlug = 'acme/support-url-plugin';
        $this->fakeGitHubForPlugin($repoSlug);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->set('supportChannel', 'https://example.com/support')
            ->call('submitPlugin')
            ->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        $this->assertNotNull($plugin);
        $this->assertEquals('https://example.com/support', $plugin->support_channel);
    }

    /** @test */
    public function submitting_a_plugin_without_support_channel_stores_null(): void
    {
        Notification::fake();
        $user = $this->createUserWithGitHub();
        $repoSlug = 'acme/no-support-plugin';
        $this->fakeGitHubForPlugin($repoSlug);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('repository', $repoSlug)
            ->set('pluginType', 'free')
            ->call('submitPlugin')
            ->assertRedirect();

        $plugin = $user->plugins()->where('repository_url', "https://github.com/{$repoSlug}")->first();

        $this->assertNotNull($plugin);
        $this->assertNull($plugin->support_channel);
    }

    /** @test */
    public function notes_and_support_channel_fields_are_hidden_until_repository_selected(): void
    {
        $user = User::factory()->create([
            'github_id' => '12345',
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertDontSee('Support Channel')
            ->assertDontSee('Any notes for the review team')
            ->set('repository', 'acme/my-plugin')
            ->assertSee('Support Channel')
            ->assertSee('Notes');
    }

    /** @test */
    public function plugin_factory_with_notes_state_works(): void
    {
        $plugin = Plugin::factory()->withNotes('Custom note')->create();

        $this->assertEquals('Custom note', $plugin->notes);
    }

    /** @test */
    public function plugin_factory_with_support_channel_state_works(): void
    {
        $plugin = Plugin::factory()->withSupportChannel('support@myplugin.dev')->create();

        $this->assertEquals('support@myplugin.dev', $plugin->support_channel);
    }
}
