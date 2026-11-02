<?php

namespace Tests\Feature;

use App\Models\Showcase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShowcasePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_showcase_page_lists_all_approved_apps(): void
    {
        Showcase::factory()->approved()->mobile()->create(['title' => 'Mobile App']);
        Showcase::factory()->approved()->desktop()->create(['title' => 'Desktop App']);
        Showcase::factory()->pending()->create(['title' => 'Pending App']);

        $response = $this->get('/showcase');

        $response->assertStatus(200);
        $response->assertSee('Mobile App');
        $response->assertSee('Desktop App');
        $response->assertDontSee('Pending App');
    }

    public function test_mobile_filter_only_shows_apps_with_mobile_support(): void
    {
        Showcase::factory()->approved()->mobile()->create(['title' => 'Mobile Only App']);
        Showcase::factory()->approved()->desktop()->create(['title' => 'Desktop Only App']);
        Showcase::factory()->approved()->both()->create(['title' => 'Cross-Platform App']);

        $response = $this->get('/showcase/mobile');

        $response->assertStatus(200);
        $response->assertSee('Mobile Only App');
        $response->assertSee('Cross-Platform App');
        $response->assertDontSee('Desktop Only App');
    }

    public function test_desktop_filter_only_shows_apps_with_desktop_support(): void
    {
        Showcase::factory()->approved()->mobile()->create(['title' => 'Mobile Only App']);
        Showcase::factory()->approved()->desktop()->create(['title' => 'Desktop Only App']);
        Showcase::factory()->approved()->both()->create(['title' => 'Cross-Platform App']);

        $response = $this->get('/showcase/desktop');

        $response->assertStatus(200);
        $response->assertSee('Desktop Only App');
        $response->assertSee('Cross-Platform App');
        $response->assertDontSee('Mobile Only App');
    }

    public function test_both_filter_only_shows_apps_supporting_both_platforms(): void
    {
        Showcase::factory()->approved()->mobile()->create(['title' => 'Mobile Only App']);
        Showcase::factory()->approved()->desktop()->create(['title' => 'Desktop Only App']);
        Showcase::factory()->approved()->both()->create(['title' => 'Cross-Platform App']);

        $response = $this->get('/showcase/both');

        $response->assertStatus(200);
        $response->assertSee('Cross-Platform App');
        $response->assertDontSee('Mobile Only App');
        $response->assertDontSee('Desktop Only App');
    }

    public function test_invalid_platform_returns_404(): void
    {
        $response = $this->get('/showcase/tablet');

        $response->assertStatus(404);
    }

    public function test_mobile_and_desktop_filters_show_platform_specific_screenshots(): void
    {
        Storage::fake('public');

        $showcase = Showcase::factory()->approved()->both()
            ->withMobileScreenshots(1)
            ->withDesktopScreenshots(1)
            ->create(['title' => 'Split Screenshots App']);

        $mobileScreenshot = $showcase->mobile_screenshots[0];
        $desktopScreenshot = $showcase->desktop_screenshots[0];

        $mobileResponse = $this->get('/showcase/mobile');
        $mobileResponse->assertSee($mobileScreenshot, false);
        $mobileResponse->assertDontSee($desktopScreenshot, false);

        $desktopResponse = $this->get('/showcase/desktop');
        $desktopResponse->assertSee($desktopScreenshot, false);
        $desktopResponse->assertDontSee($mobileScreenshot, false);
    }

    public function test_falls_back_to_shared_screenshots_without_platform_override(): void
    {
        Storage::fake('public');

        $showcase = Showcase::factory()->approved()->both()->withScreenshots(1)->create([
            'title' => 'Shared Screenshots App',
        ]);

        $sharedScreenshot = $showcase->screenshots[0];

        $this->get('/showcase/mobile')->assertSee($sharedScreenshot, false);
        $this->get('/showcase/desktop')->assertSee($sharedScreenshot, false);
    }
}
