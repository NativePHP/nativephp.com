<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MobileChangelogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        // Order matters: the GitHub stub must be registered before the
        // catch-all Torchlight stub, because Laravel serves the first
        // matching fake.
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                [
                    'id' => 1,
                    'tag_name' => 'v4.0.0-rc.2',
                    'name' => 'v4.0.0-rc.2',
                    'body' => 'Fixed gesture handling in the SuperNative renderer by @simonhamp in https://github.com/NativePHP/mobile-air/pull/123',
                    'published_at' => '2026-07-15T00:00:00Z',
                ],
                [
                    'id' => 2,
                    'tag_name' => 'v4.0.0-rc.1',
                    'name' => 'v4.0.0-rc.1',
                    'body' => 'First release candidate. See [SuperNative](https://nativephp.com/docs/mobile/4/architecture/super-native) for the full story.',
                    'published_at' => '2026-07-01T00:00:00Z',
                ],
                [
                    'id' => 3,
                    'tag_name' => 'v3.2.0',
                    'name' => 'v3.2.0',
                    'body' => 'A v3 release that must not appear on the v4 page',
                    'published_at' => '2026-05-01T00:00:00Z',
                ],
            ]),
            // The page contains fenced code blocks; Torchlight throws outside
            // production without a token (as in CI), so fake its API too.
            '*' => Http::response(['blocks' => []], 200),
        ]);

        config(['torchlight.token' => 'test-token']);
    }

    public function test_the_v4_changelog_lists_release_candidates_from_github(): void
    {
        $this->get('/docs/mobile/4/getting-started/changelog')
            ->assertOk()
            ->assertSeeInOrder(['v4.0.0-rc.2', 'v4.0.0-rc.1'])
            ->assertSee('Released: July 15, 2026')
            ->assertSee('First release candidate')
            // The hand-written v4.0 section was replaced by the automated feed.
            ->assertDontSee('v4.0 — SuperNative (beta)');
    }

    public function test_the_v4_changelog_shows_a_fallback_when_no_releases_exist(): void
    {
        // Pre-seed the releases cache so the page renders the @empty branch
        // without fighting the Http fakes registered in setUp.
        Cache::put('nativephp/mobile-air-releases', collect());

        $this->get('/docs/mobile/4/getting-started/changelog')
            ->assertOk()
            ->assertSee('Release notes for v4 will appear here')
            ->assertDontSee('v4.0.0-rc.2');
    }

    public function test_the_v4_changelog_excludes_v3_releases(): void
    {
        $this->get('/docs/mobile/4/getting-started/changelog')
            ->assertOk()
            ->assertDontSee('A v3 release that must not appear');
    }

    public function test_release_bodies_link_pull_requests_and_authors(): void
    {
        $this->get('/docs/mobile/4/getting-started/changelog')
            ->assertOk()
            ->assertSee('https://github.com/NativePHP/mobile-air/pull/123', false)
            ->assertSee('https://github.com/simonhamp', false);
    }

    /**
     * Hand-written release notes already contain Markdown links; re-wrapping
     * their URLs nests the link syntax and the page renders both the text
     * and the URL.
     */
    public function test_markdown_links_in_release_bodies_render_as_single_links(): void
    {
        $this->get('/docs/mobile/4/getting-started/changelog')
            ->assertOk()
            ->assertSee('href="https://nativephp.com/docs/mobile/4/architecture/super-native"', false)
            ->assertDontSee('[SuperNative]', false)
            ->assertDontSee('](https://nativephp.com', false);
    }

    public function test_the_v3_changelog_does_not_list_v4_prereleases(): void
    {
        $this->get('/docs/mobile/3/getting-started/changelog')
            ->assertOk()
            ->assertSee('v3.2.0')
            ->assertDontSee('4.0.0-rc');
    }
}
