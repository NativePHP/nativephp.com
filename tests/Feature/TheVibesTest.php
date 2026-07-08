<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class TheVibesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
    }

    /** @test */
    public function the_vibes_page_lists_jump24_as_a_sponsor(): void
    {
        $response = $this->get(route('the-vibes'));

        $response->assertStatus(200);
        $response->assertSee('Jump24');
        $response->assertSee('jump24.co.uk');
    }

    /** @test */
    public function the_vibes_page_still_lists_existing_sponsors(): void
    {
        $response = $this->get(route('the-vibes'));

        $response->assertSee('Web Mavens');
        $response->assertSee('Nexcalia');
        $response->assertSee('Nopticon');
    }

    /** @test */
    public function prospectus_shows_current_partners_including_jump24(): void
    {
        $response = $this->get(route('the-vibes-prospectus'));

        $response->assertStatus(200);
        $response->assertSee('Already On Board');
        $response->assertSee('Jump24');
        $response->assertSee('jump24.co.uk');
        $response->assertSee('Web Mavens');
    }

    /** @test */
    public function prospectus_advertises_food_and_venue_partners(): void
    {
        $response = $this->get(route('the-vibes-prospectus'));

        $response->assertSee('Food & Venue Partners');
        $response->assertSee('Food & Drink Partner');
        $response->assertSee('Venue Partner');
    }

    /** @test */
    public function both_pages_render_the_vibes_header_logo(): void
    {
        foreach (['the-vibes', 'the-vibes-prospectus'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('/img/the-vibes/logo.svg')
                ->assertSee('/img/the-vibes/logo-dark.svg')
                ->assertSee('The Vibes, hosted by NativePHP');
        }
    }

    /** @test */
    public function header_logo_assets_exist(): void
    {
        $this->assertFileExists(public_path('img/the-vibes/logo.svg'));
        $this->assertFileExists(public_path('img/the-vibes/logo-dark.svg'));
    }

    /** @test */
    public function the_vibes_sponsor_cta_links_to_the_prospectus(): void
    {
        $response = $this->get(route('the-vibes'));

        $response->assertOk();
        $response->assertSee('Interested in sponsoring The Vibes?');
        $response->assertSee('View the Prospectus');
        $response->assertSee(route('the-vibes-prospectus'), false);
        $response->assertDontSee('The%20Vibes%20-%20Sponsorship%20Inquiry');
    }

    /** @test */
    public function the_vibes_page_shows_the_event_schedule(): void
    {
        $response = $this->get(route('the-vibes'));

        $response->assertOk();
        $response->assertSee('Schedule');
        $response->assertSee('9:00');
        $response->assertSee('Doors open & Breakfast');
        $response->assertSee('Coffee and pastries in The Assembly');
        $response->assertSee('The Future is Here');
        $response->assertSee('Simon & Shane');
        $response->assertSee('Lightning talks');
        $response->assertSee('Special guests');
        $response->assertSee('Lunch');
        $response->assertSee('The Atrium');
        $response->assertSee('Chill, network, vibecode, hack');
        $response->assertSee('17:00');
    }
}
