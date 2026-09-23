<?php

namespace Tests\Feature\Docs;

use App\Features\ShowPlugins;
use App\Support\JumpApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class JumpBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected string $fixturesDir;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);

        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);

        // testing runs with CACHE_DRIVER=array, but cacheOrCompute() still
        // caches (only `local` bypasses it) — flush so a fixture written by
        // one test can't be served stale to the next.
        Cache::flush();

        $this->fixturesDir = resource_path('views/docs/mobile/4/edge-components');
    }

    protected function tearDown(): void
    {
        foreach (['unavailable', 'unshipped', 'shipped'] as $name) {
            @unlink("{$this->fixturesDir}/_test-jump-badge-{$name}.md");
        }

        Cache::flush();

        parent::tearDown();
    }

    protected function writeFixture(string $name, string $frontMatterExtra): void
    {
        file_put_contents(
            "{$this->fixturesDir}/_test-jump-badge-{$name}.md",
            <<<MD
            ---
            title: Jump badge fixture ({$name})
            order: 999
            {$frontMatterExtra}
            ---

            Fixture page for JumpBadgeTest.
            MD
        );
    }

    public function test_supports_with_no_requirement(): void
    {
        $this->assertTrue(JumpApp::supports(null));
    }

    public function test_supports_when_no_build_has_it(): void
    {
        $this->assertFalse(JumpApp::supports(false));
    }

    public function test_supports_an_older_or_equal_version(): void
    {
        config(['docs.jump.current_version' => '2.2']);

        $this->assertTrue(JumpApp::supports('2.0'));
        $this->assertTrue(JumpApp::supports('2.2'));
    }

    public function test_does_not_support_a_newer_version(): void
    {
        config(['docs.jump.current_version' => '2.0']);

        $this->assertFalse(JumpApp::supports('2.2'));
    }

    public function test_page_with_jump_false_shows_not_in_jump_yet_and_no_qr(): void
    {
        $this->writeFixture('unavailable', 'jump: false');

        $this->get('/docs/mobile/4/edge-components/_test-jump-badge-unavailable')
            ->assertStatus(200)
            ->assertSee('Not in Jump yet')
            ->assertDontSee('Preview in Jump');
    }

    public function test_page_with_unshipped_jump_version_shows_pill_and_no_qr(): void
    {
        config(['docs.jump.current_version' => '2.0']);
        $this->writeFixture('unshipped', 'jump: "99.0"');

        $this->get('/docs/mobile/4/edge-components/_test-jump-badge-unshipped')
            ->assertStatus(200)
            ->assertSee('Jump 99.0+')
            ->assertDontSee('Preview in Jump');
    }

    public function test_page_with_shipped_jump_version_shows_no_pill_and_the_qr(): void
    {
        config(['docs.jump.current_version' => '2.0']);
        $this->writeFixture('shipped', 'jump: "1.0"');

        $response = $this->get('/docs/mobile/4/edge-components/_test-jump-badge-shipped')
            ->assertStatus(200)
            ->assertDontSee('Not in Jump yet')
            ->assertDontSee('Jump 1.0+')
            ->assertSee('Preview in Jump');

        $response->assertOk();
    }
}
