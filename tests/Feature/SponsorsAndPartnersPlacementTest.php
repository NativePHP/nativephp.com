<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SponsorsAndPartnersPlacementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_partners_section_no_longer_lists_beyondcode_or_laradevs()
    {
        $this->blade('<x-home.partners />')
            ->assertSee('Nexcalia')
            ->assertSee('Web Mavens')
            ->assertDontSee('Laradevs')
            ->assertDontSee('BeyondCode')
            ->assertDontSee('Need a freelancer or engineer?')
            ->assertDontSee('From local full stack development');
    }

    #[Test]
    public function the_sponsors_section_lists_beyondcode_and_laradevs_without_descriptions()
    {
        $this->blade('<x-home.sponsors />')
            ->assertSee('Artisan Build')
            ->assertSee('BeyondCode')
            ->assertSee('Laradevs')
            ->assertDontSee('Need a freelancer or engineer?')
            ->assertDontSee('From local full stack development');
    }

    #[Test]
    public function the_homepage_drops_the_partner_descriptions_for_beyondcode_and_laradevs()
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Need a freelancer or engineer?')
            ->assertDontSee('From local full stack development');
    }

    #[Test]
    public function the_partners_page_lists_the_current_partners()
    {
        $this->get(route('partners'))
            ->assertOk()
            ->assertSee('Meet Our Partners')
            ->assertSee('Nexcalia')
            ->assertSee('Web Mavens')
            ->assertSee('Synergi Tech');
    }
}
