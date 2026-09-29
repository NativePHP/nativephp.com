<?php

namespace Tests\Feature\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublishingAppEnvTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // These pages render fenced code blocks; Torchlight throws outside
        // production without a token, so fake its offline fallback.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    public function test_publishing_introduction_explains_that_only_production_builds_reach_the_public(): void
    {
        $this->withoutVite()
            ->get('/docs/mobile/4/publishing/introduction')
            ->assertOk()
            ->assertSee('id="production-and-testing-builds"', false)
            ->assertSee('>4.5<', false)
            ->assertSee('APP_ENV=production')
            ->assertSee('TestFlight internal testing only')
            ->assertSee('#production-and-testing-builds', false);
    }

    public function test_android_page_keeps_testing_builds_off_the_open_and_production_tracks(): void
    {
        $this->withoutVite()
            ->get('/docs/mobile/4/publishing/android')
            ->assertOk()
            ->assertSee('id="releasing-to-production"', false)
            ->assertSee('introduction#production-and-testing-builds', false)
            ->assertSee('Open testing')
            ->assertDontSee('Closed beta testing');
    }

    public function test_ios_page_explains_that_app_store_testing_builds_stay_in_internal_testflight(): void
    {
        $this->withoutVite()
            ->get('/docs/mobile/4/publishing/ios')
            ->assertOk()
            ->assertSee('id="testing-builds"', false)
            ->assertSee('introduction#production-and-testing-builds', false)
            ->assertSee('TestFlight internal testing only');
    }

    public function test_upgrade_guide_tells_4_4_apps_to_package_releases_with_production(): void
    {
        $this->withoutVite()
            ->get('/docs/mobile/4/getting-started/upgrade-guide')
            ->assertOk()
            ->assertSeeInOrder(['Upgrading To 4.5 From 4.4', 'APP_ENV=production', 'Upgrading To 4.0 From 3.x'])
            ->assertSee('publishing/introduction#production-and-testing-builds', false);
    }
}
