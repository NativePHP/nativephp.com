<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupernativeBannerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_site_banner_announces_supernative_v4()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('NativePHP for Mobile v4')
            ->assertSee('/docs/mobile/4/architecture/super-native', escape: false)
            ->assertSee('supernative_banner_click', escape: false)
            // The Vibes event banner is replaced.
            ->assertDontSee('Get your ticket');
    }
}
