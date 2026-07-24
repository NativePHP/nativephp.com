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

        $this->assertTrue(Cache::has('docs_mobile_4_edge-components/stack'));
    }
}
