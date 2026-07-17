<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SponsorsAndPartnersPlacementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_partners_page_renders_the_partners_section()
    {
        $this->get(route('partners'))
            ->assertOk()
            ->assertSee('Meet Our Partners');
    }

    #[Test]
    public function the_sidebar_partners_list_rotates_through_every_entry_it_is_given()
    {
        $partners = [
            ['url' => 'https://partner-one.test', 'name' => 'Partner One', 'tagline' => 'First', 'image' => '/img/one.svg', 'imageDark' => '/img/one-dark.svg', 'class' => ''],
            ['url' => 'https://partner-two.test', 'name' => 'Partner Two', 'tagline' => 'Second', 'image' => '/img/two.svg', 'imageDark' => '/img/two-dark.svg', 'class' => ''],
        ];

        $this->blade('<x-sponsors.lists.docs.featured-sponsors :partners="$partners" />', compact('partners'))
            ->assertSee('Math.random() * 2', false)
            ->assertSee('x-show="partner === 0"', false)
            ->assertSee('x-show="partner === 1"', false)
            ->assertSee('https://partner-one.test')
            ->assertSee('https://partner-two.test');
    }

    #[Test]
    public function the_sidebar_sponsors_list_rotates_through_every_entry_it_is_given()
    {
        $sponsors = [
            ['url' => 'https://sponsor-one.test', 'name' => 'Sponsor One', 'image' => '/img/one.svg', 'imageDark' => '/img/one-dark.svg', 'class' => ''],
            ['url' => 'https://sponsor-two.test', 'name' => 'Sponsor Two', 'image' => '/img/two.svg', 'imageDark' => '/img/two-dark.svg', 'class' => ''],
        ];

        $this->blade('<x-sponsors.lists.docs.sponsors :sponsors="$sponsors" />', compact('sponsors'))
            ->assertSee('Math.random() * 2', false)
            ->assertSee('x-show="sponsor === 0"', false)
            ->assertSee('x-show="sponsor === 1"', false)
            ->assertSee('https://sponsor-one.test')
            ->assertSee('https://sponsor-two.test');
    }
}
