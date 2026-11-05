<?php

namespace Tests\Feature\Support;

use App\Support\GitHub;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_mobile_air_fetches_releases_from_the_correct_repo(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                [
                    'id' => 1,
                    'name' => 'v3.2.0',
                    'tag_name' => 'v3.2.0',
                    'body' => 'New release',
                    'published_at' => '2026-05-01T00:00:00Z',
                ],
            ]),
        ]);

        $releases = GitHub::mobileAir()->releases();

        $this->assertCount(1, $releases);
        $this->assertEquals('v3.2.0', $releases->first()->tag_name);

        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://api.github.com/repos/nativephp/mobile-air/releases?'));
    }

    public function test_releases_paginates_through_every_page(): void
    {
        $firstPage = array_map(
            fn (int $i) => ['id' => $i, 'tag_name' => "v1.{$i}.0", 'name' => "v1.{$i}.0", 'body' => '', 'published_at' => '2026-01-01T00:00:00Z'],
            range(1, 100),
        );
        $secondPage = [
            ['id' => 101, 'tag_name' => 'v0.9.0', 'name' => 'v0.9.0', 'body' => '', 'published_at' => '2025-12-01T00:00:00Z'],
        ];

        $sequence = Http::sequence()
            ->push($firstPage)
            ->push($secondPage);

        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => $sequence,
        ]);

        $releases = GitHub::mobileAir()->releases();

        $this->assertCount(101, $releases);
        $this->assertEquals('v0.9.0', $releases->last()->tag_name);
        Http::assertSentCount(2);
    }

    public function test_releases_after_filters_out_versions_at_or_below_the_given_version(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                ['id' => 1, 'tag_name' => 'v4.0.0', 'name' => 'v4.0.0', 'body' => '', 'published_at' => '2026-06-01T00:00:00Z'],
                ['id' => 2, 'tag_name' => 'v3.2.0', 'name' => 'v3.2.0', 'body' => '', 'published_at' => '2026-05-01T00:00:00Z'],
                ['id' => 3, 'tag_name' => 'v3.1.1', 'name' => 'v3.1.1', 'body' => '', 'published_at' => '2026-04-15T00:00:00Z'],
                ['id' => 4, 'tag_name' => 'v3.1.0', 'name' => 'v3.1.0', 'body' => '', 'published_at' => '2026-04-01T00:00:00Z'],
                ['id' => 5, 'tag_name' => 'v3.0.0', 'name' => 'v3.0.0', 'body' => '', 'published_at' => '2026-03-01T00:00:00Z'],
            ]),
        ]);

        $releases = GitHub::mobileAir()->releasesAfter('3.1.0');

        $this->assertEquals(
            ['v4.0.0', 'v3.2.0', 'v3.1.1'],
            $releases->pluck('tag_name')->all()
        );
    }

    public function test_releases_after_handles_tag_names_without_a_leading_v(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                ['id' => 1, 'tag_name' => '3.2.0', 'name' => '3.2.0', 'body' => '', 'published_at' => '2026-05-01T00:00:00Z'],
                ['id' => 2, 'tag_name' => '3.1.0', 'name' => '3.1.0', 'body' => '', 'published_at' => '2026-04-01T00:00:00Z'],
            ]),
        ]);

        $releases = GitHub::mobileAir()->releasesAfter('v3.1.0');

        $this->assertCount(1, $releases);
        $this->assertEquals('3.2.0', $releases->first()->tag_name);
    }

    public function test_releases_after_caps_below_the_next_major_including_its_prereleases(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                ['id' => 1, 'tag_name' => 'v4.0.0', 'name' => 'v4.0.0', 'body' => '', 'published_at' => '2026-08-01T00:00:00Z'],
                ['id' => 2, 'tag_name' => 'v4.0.0-rc.1', 'name' => 'v4.0.0-rc.1', 'body' => '', 'published_at' => '2026-07-01T00:00:00Z'],
                ['id' => 3, 'tag_name' => 'v3.2.0', 'name' => 'v3.2.0', 'body' => '', 'published_at' => '2026-05-01T00:00:00Z'],
                ['id' => 4, 'tag_name' => 'v3.1.0', 'name' => 'v3.1.0', 'body' => '', 'published_at' => '2026-04-01T00:00:00Z'],
            ]),
        ]);

        $releases = GitHub::mobileAir()->releasesAfter('3.1.0', before: '4.0.0');

        $this->assertEquals(['v3.2.0'], $releases->pluck('tag_name')->all());
    }

    public function test_releases_from_includes_prereleases_of_the_boundary_version(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                ['id' => 1, 'tag_name' => 'v4.0.1', 'name' => 'v4.0.1', 'body' => '', 'published_at' => '2026-08-01T00:00:00Z'],
                ['id' => 2, 'tag_name' => 'v4.0.0', 'name' => 'v4.0.0', 'body' => '', 'published_at' => '2026-07-20T00:00:00Z'],
                ['id' => 3, 'tag_name' => 'v4.0.0-rc.2', 'name' => 'v4.0.0-rc.2', 'body' => '', 'published_at' => '2026-07-10T00:00:00Z'],
                ['id' => 4, 'tag_name' => 'v4.0.0-rc.1', 'name' => 'v4.0.0-rc.1', 'body' => '', 'published_at' => '2026-07-01T00:00:00Z'],
                ['id' => 5, 'tag_name' => 'v4.0.0-beta.1', 'name' => 'v4.0.0-beta.1', 'body' => '', 'published_at' => '2026-06-15T00:00:00Z'],
                ['id' => 6, 'tag_name' => 'v3.2.9', 'name' => 'v3.2.9', 'body' => '', 'published_at' => '2026-06-01T00:00:00Z'],
                ['id' => 7, 'tag_name' => 'v3.2.0', 'name' => 'v3.2.0', 'body' => '', 'published_at' => '2026-05-01T00:00:00Z'],
            ]),
        ]);

        $releases = GitHub::mobileAir()->releasesFrom('4.0.0');

        $this->assertEquals(
            ['v4.0.1', 'v4.0.0', 'v4.0.0-rc.2', 'v4.0.0-rc.1', 'v4.0.0-beta.1'],
            $releases->pluck('tag_name')->all()
        );
    }

    public function test_releases_from_handles_tag_names_without_a_leading_v(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response([
                ['id' => 1, 'tag_name' => '4.0.0-rc.1', 'name' => '4.0.0-rc.1', 'body' => '', 'published_at' => '2026-07-01T00:00:00Z'],
                ['id' => 2, 'tag_name' => '3.2.0', 'name' => '3.2.0', 'body' => '', 'published_at' => '2026-05-01T00:00:00Z'],
            ]),
        ]);

        $releases = GitHub::mobileAir()->releasesFrom('v4.0.0');

        $this->assertCount(1, $releases);
        $this->assertEquals('4.0.0-rc.1', $releases->first()->tag_name);
    }

    public function test_releases_after_returns_empty_collection_when_request_fails(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/releases*' => Http::response(null, 500),
        ]);

        $this->assertTrue(GitHub::mobileAir()->releasesAfter('3.1.0')->isEmpty());
    }

    public function test_tree_fetches_the_recursive_tree_of_the_default_branch(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/' => Http::response(['default_branch' => 'develop']),
            'api.github.com/repos/nativephp/mobile-air/git/trees/develop*' => Http::response([
                'tree' => [
                    ['path' => 'config/nativephp.php', 'type' => 'blob', 'sha' => 'abc123'],
                ],
                'truncated' => false,
            ]),
        ]);

        $tree = GitHub::mobileAir()->tree();

        $this->assertCount(1, $tree);
        $this->assertSame('config/nativephp.php', $tree->first()['path']);
        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://api.github.com/repos/nativephp/mobile-air/git/trees/develop?'));
    }

    public function test_tree_falls_back_to_main_when_the_default_branch_lookup_fails(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/' => Http::response(null, 404),
            'api.github.com/repos/nativephp/mobile-air/git/trees/main*' => Http::response(['tree' => [], 'truncated' => false]),
        ]);

        GitHub::mobileAir()->tree();

        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://api.github.com/repos/nativephp/mobile-air/git/trees/main?'));
    }

    public function test_tree_returns_an_empty_collection_when_the_request_fails(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/' => Http::response(['default_branch' => 'main']),
            'api.github.com/repos/nativephp/mobile-air/git/trees/main*' => Http::response(null, 500),
        ]);

        $this->assertTrue(GitHub::mobileAir()->tree()->isEmpty());
    }

    public function test_blob_decodes_the_base64_content(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/git/blobs/abc123' => Http::response([
                'content' => base64_encode("<?php\nreturn ['foo' => 'bar'];\n"),
                'encoding' => 'base64',
            ]),
        ]);

        $this->assertSame("<?php\nreturn ['foo' => 'bar'];\n", GitHub::mobileAir()->blob('abc123'));
    }

    public function test_blob_returns_null_when_the_request_fails(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air/git/blobs/missing' => Http::response(null, 404),
        ]);

        $this->assertNull(GitHub::mobileAir()->blob('missing'));
    }

    public function test_requests_are_authenticated_when_a_github_token_is_configured(): void
    {
        config(['services.github.token' => 'test-token']);

        Http::fake([
            'api.github.com/*' => Http::response(['default_branch' => 'main']),
        ]);

        GitHub::mobileAir()->tree();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_requests_carry_no_authorization_header_without_a_configured_token(): void
    {
        config(['services.github.token' => null]);

        Http::fake([
            'api.github.com/*' => Http::response(['default_branch' => 'main']),
        ]);

        GitHub::mobileAir()->tree();

        Http::assertSent(fn ($request) => ! $request->hasHeader('Authorization'));
    }
}
