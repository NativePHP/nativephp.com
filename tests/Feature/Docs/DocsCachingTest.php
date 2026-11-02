<?php

namespace Tests\Feature\Docs;

use App\Features\ShowPlugins;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class DocsCachingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);

        // These tests render full docs pages that contain fenced code blocks.
        // Torchlight throws outside production when no token is configured (as
        // in CI), so give it a token and fake the API to force its offline
        // fallback — the pages render deterministically without a real token.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    public function test_local_docs_request_does_not_flush_the_entire_cache(): void
    {
        config(['app.env' => 'local']);
        Cache::put('unrelated-key', 'keep-me', now()->addHour());

        $this->get('/docs/mobile/4/edge-components/stack')->assertStatus(200);

        $this->assertSame('keep-me', Cache::get('unrelated-key'));
    }

    public function test_local_docs_request_does_not_cache_page_properties(): void
    {
        config(['app.env' => 'local']);

        $this->get('/docs/mobile/4/edge-components/stack')->assertStatus(200);

        $this->assertFalse(Cache::has('docs_mobile_4_edge-components/stack'));
    }

    public function test_non_local_docs_request_caches_page_properties(): void
    {
        config(['app.env' => 'production']);

        $this->get('/docs/mobile/4/edge-components/stack')->assertStatus(200);

        // The key is suffixed with a hash of config('docs') so a Jump version
        // bump invalidates rendered pages instead of trailing by up to a day.
        $key = 'docs_mobile_4_edge-components/stack_'.substr(md5(serialize(config('docs'))), 0, 8);

        $this->assertTrue(Cache::has($key));
    }

    public function test_page_cache_is_reused_while_the_markdown_is_unchanged(): void
    {
        config(['app.env' => 'production']);

        $this->get('/docs/mobile/4/edge-components/stack')->assertStatus(200);

        $entry = Cache::get($this->pageCacheKey());
        $entry['value']['content'] = '<p>Served from the cache</p>';
        Cache::put($this->pageCacheKey(), $entry, now()->addDay());

        $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->assertSee('Served from the cache', false);
    }

    public function test_page_cache_is_rebuilt_once_the_markdown_changes(): void
    {
        config(['app.env' => 'production']);

        Cache::put($this->pageCacheKey(), [
            'fingerprint' => 'built-from-older-markdown',
            'value' => $this->stalePageProperties(),
        ], now()->addDay());

        $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->assertDontSee('Stale content');
    }

    public function test_page_cached_before_fingerprinting_is_rebuilt(): void
    {
        config(['app.env' => 'production']);

        Cache::put($this->pageCacheKey(), $this->stalePageProperties(), now()->addDay());

        $this->get('/docs/mobile/4/edge-components/stack')
            ->assertStatus(200)
            ->assertDontSee('Stale content');
    }

    public function test_navigation_cached_before_fingerprinting_is_rebuilt(): void
    {
        config(['app.env' => 'production']);

        $key = 'docs_nav_mobile_4_'.substr(md5(serialize(config('docs'))), 0, 8);
        Cache::put($key, [['path' => '/docs/mobile/4/removed-page', 'title' => 'Removed', 'order' => 0]], now()->addDay());

        $response = $this->get('/docs/mobile/4');

        $response->assertStatus(301);
        $this->assertStringNotContainsString('removed-page', (string) $response->headers->get('Location'));
    }

    private function pageCacheKey(): string
    {
        return 'docs_mobile_4_edge-components/stack_'.substr(md5(serialize(config('docs'))), 0, 8);
    }

    /**
     * Page properties in the shape the view expects, so a cache entry that is
     * wrongly trusted renders rather than erroring — the assertion, not an
     * exception, is what reports the failure.
     *
     * @return array<string, mixed>
     */
    private function stalePageProperties(): array
    {
        return [
            'platform' => 'mobile',
            'version' => '4',
            'pagePath' => 'docs/mobile/4/edge-components/stack',
            'title' => 'Stale',
            'content' => '<p>Stale content</p>',
            'tableOfContents' => [],
            'navigation' => '',
            'editUrl' => '',
            'nextPage' => null,
            'previousPage' => null,
        ];
    }
}
