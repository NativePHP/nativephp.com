<?php

namespace Tests\Feature;

use App\Enums\PluginStatus;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Plugins\Show;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginReviewChecksIncomplete;
use App\Rules\DemoVideoUrl;
use App\Support\DemoVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PluginDemoVideoTest extends TestCase
{
    use RefreshDatabase;

    private const YOUTUBE_URL = 'https://www.youtube.com/watch?v=abcdefghijk';

    private const PASSING_REPO_CHECKS = [
        'has_license_file' => true,
        'has_release_version' => true,
        'release_version' => 'v1.0.0',
    ];

    private function createGitHubUser(): User
    {
        return User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_token' => encrypt('fake-token'),
        ]);
    }

    private function createDraftPlugin(User $user, array $attributes = []): Plugin
    {
        return Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/demo-plugin-'.fake()->unique()->numberBetween(100, 999999),
            'support_channel' => 'support@test.io',
            ...$attributes,
        ]);
    }

    private function mountShowComponent(User $user, Plugin $plugin): Testable
    {
        [$vendor, $package] = explode('/', $plugin->name);

        return Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ]);
    }

    // ========================================
    // DemoVideo URL parsing
    // ========================================

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function supportedVideoUrls(): array
    {
        return [
            'youtube watch' => ['https://www.youtube.com/watch?v=abcdefghijk', 'youtube', 'abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
            'youtube watch with extra params' => ['https://youtube.com/watch?v=abcdefghijk&t=42s', 'youtube', 'abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
            'youtube mobile' => ['https://m.youtube.com/watch?v=abcdefghijk', 'youtube', 'abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
            'youtube short link' => ['https://youtu.be/abcdefghijk', 'youtube', 'abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
            'youtube shorts' => ['https://www.youtube.com/shorts/abcdefghijk', 'youtube', 'abcdefghijk', 'https://www.youtube-nocookie.com/embed/abcdefghijk'],
            'vimeo' => ['https://vimeo.com/123456789', 'vimeo', '123456789', 'https://player.vimeo.com/video/123456789'],
            'vimeo unlisted' => ['https://vimeo.com/123456789/a1b2c3d4e5', 'vimeo', '123456789', 'https://player.vimeo.com/video/123456789?h=a1b2c3d4e5'],
            'loom share' => ['https://www.loom.com/share/0123456789abcdef', 'loom', '0123456789abcdef', 'https://www.loom.com/embed/0123456789abcdef'],
        ];
    }

    #[DataProvider('supportedVideoUrls')]
    public function test_supported_video_urls_are_parsed(string $url, string $provider, string $videoId, string $embedUrl): void
    {
        $video = DemoVideo::fromUrl($url);

        $this->assertNotNull($video);
        $this->assertSame($provider, $video->provider);
        $this->assertSame($videoId, $video->videoId);
        $this->assertSame($embedUrl, $video->embedUrl());
    }

    /**
     * @return array<string, array{0: ?string}>
     */
    public static function unsupportedVideoUrls(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'not a url' => ['my plugin video'],
            'unsupported host' => ['https://example.com/video.mp4'],
            'lookalike host' => ['https://youtube.com.evil.test/watch?v=abcdefghijk'],
            'youtube channel' => ['https://www.youtube.com/@nativephp'],
            'youtube watch without id' => ['https://www.youtube.com/watch'],
            'youtube malformed id' => ['https://youtu.be/short'],
            'vimeo non numeric' => ['https://vimeo.com/channels/staffpicks'],
            'ftp scheme' => ['ftp://vimeo.com/123456789'],
        ];
    }

    #[DataProvider('unsupportedVideoUrls')]
    public function test_unsupported_video_urls_are_rejected(?string $url): void
    {
        $this->assertNull(DemoVideo::fromUrl($url));
    }

    // ========================================
    // DemoVideoUrl validation rule
    // ========================================

    public function test_rule_passes_when_provider_confirms_video_exists(): void
    {
        Http::fake(['https://www.youtube.com/oembed*' => Http::response(['title' => 'Demo'], 200)]);

        $validator = Validator::make(['url' => self::YOUTUBE_URL], ['url' => [new DemoVideoUrl]]);

        $this->assertTrue($validator->passes());
        Http::assertSent(fn ($request) => str_contains($request->url(), urlencode('https://www.youtube.com/watch?v=abcdefghijk')));
    }

    public function test_rule_fails_for_private_or_missing_video(): void
    {
        Http::fake(['https://www.youtube.com/oembed*' => Http::response('Unauthorized', 401)]);

        $validator = Validator::make(['url' => self::YOUTUBE_URL], ['url' => [new DemoVideoUrl]]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('public or unlisted', $validator->errors()->first('url'));
    }

    public function test_rule_fails_for_unsupported_host_without_calling_provider(): void
    {
        Http::preventStrayRequests();

        $validator = Validator::make(['url' => 'https://example.com/demo.mp4'], ['url' => [new DemoVideoUrl]]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('YouTube, Vimeo or Loom', $validator->errors()->first('url'));
    }

    public function test_rule_fails_gracefully_when_provider_is_unreachable(): void
    {
        Http::fake(['https://vimeo.com/api/oembed.json*' => Http::failedConnection()]);

        $validator = Validator::make(['url' => 'https://vimeo.com/123456789'], ['url' => [new DemoVideoUrl]]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString("couldn't reach Vimeo", $validator->errors()->first('url'));
    }

    // ========================================
    // Saving the demo video (developer dashboard)
    // ========================================

    public function test_developer_can_save_a_verified_demo_video(): void
    {
        Http::fake(['https://www.youtube.com/oembed*' => Http::response(['title' => 'Demo'], 200)]);
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, ['demo_video_url' => null, 'demo_video_attested_at' => null]);

        $this->mountShowComponent($user, $plugin)
            ->set('demoVideoUrl', self::YOUTUBE_URL)
            ->set('demoVideoAttested', true)
            ->call('save')
            ->assertHasNoErrors();

        $plugin->refresh();
        $this->assertSame(self::YOUTUBE_URL, $plugin->demo_video_url);
        $this->assertNotNull($plugin->demo_video_attested_at);
        $this->assertTrue($plugin->hasDemoVideo());
    }

    public function test_saving_a_demo_video_requires_attestation(): void
    {
        Http::fake(['https://www.youtube.com/oembed*' => Http::response(['title' => 'Demo'], 200)]);
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, ['demo_video_url' => null, 'demo_video_attested_at' => null]);

        $this->mountShowComponent($user, $plugin)
            ->set('demoVideoUrl', self::YOUTUBE_URL)
            ->set('demoVideoAttested', false)
            ->call('save')
            ->assertHasErrors(['demoVideoAttested']);

        $this->assertNull($plugin->fresh()->demo_video_url);
    }

    public function test_saving_a_private_video_is_rejected(): void
    {
        Http::fake(['https://www.youtube.com/oembed*' => Http::response('Unauthorized', 401)]);
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, ['demo_video_url' => null, 'demo_video_attested_at' => null]);

        $this->mountShowComponent($user, $plugin)
            ->set('demoVideoUrl', self::YOUTUBE_URL)
            ->set('demoVideoAttested', true)
            ->call('save')
            ->assertHasErrors(['demoVideoUrl']);

        $this->assertNull($plugin->fresh()->demo_video_url);
    }

    public function test_saving_an_unsupported_video_host_is_rejected(): void
    {
        Http::preventStrayRequests();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, ['demo_video_url' => null, 'demo_video_attested_at' => null]);

        $this->mountShowComponent($user, $plugin)
            ->set('demoVideoUrl', 'https://example.com/demo.mp4')
            ->set('demoVideoAttested', true)
            ->call('save')
            ->assertHasErrors(['demoVideoUrl']);
    }

    public function test_unchanged_demo_video_is_not_reverified_on_save(): void
    {
        Http::preventStrayRequests();
        $user = $this->createGitHubUser();
        $attestedAt = now()->subDays(3)->startOfSecond();
        $plugin = $this->createDraftPlugin($user, [
            'demo_video_url' => self::YOUTUBE_URL,
            'demo_video_attested_at' => $attestedAt,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->set('description', 'Updated description')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue($plugin->fresh()->demo_video_attested_at->equalTo($attestedAt));
    }

    public function test_changing_the_demo_video_refreshes_attestation(): void
    {
        Http::fake(['https://vimeo.com/api/oembed.json*' => Http::response(['title' => 'Demo'], 200)]);
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, [
            'demo_video_url' => self::YOUTUBE_URL,
            'demo_video_attested_at' => now()->subDays(3),
        ]);

        $this->mountShowComponent($user, $plugin)
            ->set('demoVideoUrl', 'https://vimeo.com/123456789')
            ->call('save')
            ->assertHasNoErrors();

        $plugin->refresh();
        $this->assertSame('https://vimeo.com/123456789', $plugin->demo_video_url);
        $this->assertTrue($plugin->demo_video_attested_at->isToday());
    }

    public function test_clearing_the_demo_video_clears_attestation(): void
    {
        Http::preventStrayRequests();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);

        $this->mountShowComponent($user, $plugin)
            ->set('demoVideoUrl', '')
            ->call('save')
            ->assertHasNoErrors();

        $plugin->refresh();
        $this->assertNull($plugin->demo_video_url);
        $this->assertNull($plugin->demo_video_attested_at);
        $this->assertFalse($plugin->hasDemoVideo());
    }

    // ========================================
    // Submission and approval gates
    // ========================================

    public function test_submit_is_blocked_without_a_demo_video(): void
    {
        Http::preventStrayRequests();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, ['demo_video_url' => null, 'demo_video_attested_at' => null]);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $this->assertEquals(PluginStatus::Draft, $plugin->fresh()->status);
    }

    public function test_submit_is_blocked_when_demo_video_is_not_attested(): void
    {
        Http::preventStrayRequests();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, ['demo_video_url' => self::YOUTUBE_URL, 'demo_video_attested_at' => null]);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $this->assertEquals(PluginStatus::Draft, $plugin->fresh()->status);
    }

    public function test_required_checks_fail_without_demo_video_even_when_repo_checks_pass(): void
    {
        $plugin = Plugin::factory()->pending()->withoutDemoVideo()->create([
            'review_checks' => self::PASSING_REPO_CHECKS,
            'webhook_installed' => true,
        ]);

        $this->assertFalse($plugin->passesRequiredReviewChecks());
        $this->assertSame(['Demo video showing the plugin working'], $plugin->getFailingRequiredChecks());
    }

    public function test_required_checks_pass_with_attested_demo_video(): void
    {
        $plugin = Plugin::factory()->pending()->create([
            'review_checks' => self::PASSING_REPO_CHECKS,
            'webhook_installed' => true,
            'demo_video_url' => self::YOUTUBE_URL,
            'demo_video_attested_at' => now(),
        ]);

        $this->assertTrue($plugin->passesRequiredReviewChecks());
        $this->assertSame([], $plugin->getFailingRequiredChecks());
    }

    // ========================================
    // Public listing
    // ========================================

    public function test_demo_video_is_hidden_on_public_listing_by_default(): void
    {
        Feature::define(ShowPlugins::class, true);
        $plugin = Plugin::factory()->approved()->create(['demo_video_url' => self::YOUTUBE_URL]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk()
            ->assertDontSee('youtube-nocookie.com/embed/abcdefghijk', false);
    }

    public function test_demo_video_is_embedded_on_public_listing_when_admin_enables_it(): void
    {
        Feature::define(ShowPlugins::class, true);
        $plugin = Plugin::factory()->approved()->withPublicDemoVideo()->create(['demo_video_url' => self::YOUTUBE_URL]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk()
            ->assertSee('https://www.youtube-nocookie.com/embed/abcdefghijk', false);
    }

    // ========================================
    // Developer notification
    // ========================================

    public function test_review_checks_email_lists_missing_demo_video(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->pending()->withoutDemoVideo()->for($user)->create([
            'review_checks' => self::PASSING_REPO_CHECKS,
            'webhook_installed' => true,
        ]);

        $rendered = (string) (new PluginReviewChecksIncomplete($plugin))->toMail($user)->render();

        $this->assertStringContainsString('Demo video (YouTube, Vimeo or Loom)', $rendered);
    }

    public function test_review_checks_email_lists_demo_video_as_passing(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'review_checks' => self::PASSING_REPO_CHECKS,
            'webhook_installed' => true,
        ]);

        $rendered = (string) (new PluginReviewChecksIncomplete($plugin))->toMail($user)->render();

        $this->assertStringContainsString('✅ Demo video', $rendered);
        $this->assertStringNotContainsString('Demo video (YouTube, Vimeo or Loom)', $rendered);
    }
}
