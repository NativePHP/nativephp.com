<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialNetworkLinksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, false);
        Feature::define(ShowPlugins::class, true);
    }

    #[Test]
    public function the_footer_links_to_dev_null_instead_of_pinkary(): void
    {
        $this
            ->withoutVite()
            ->get('/contributing')
            ->assertOk()
            ->assertSee('href="https://dev-null.sh/@nativephp"', false)
            ->assertSee('title="NativePHP on /dev/null"', false)
            ->assertDontSee('pinkary.com')
            ->assertDontSee('Pinkary');
    }

    #[Test]
    public function the_footer_lists_every_network_in_the_chosen_order(): void
    {
        $this
            ->withoutVite()
            ->get('/contributing')
            ->assertOk()
            ->assertSeeInOrder([
                'title="Source code of NativePHP"',
                'title="NativePHP on Open Collective"',
                'title="Go to discord server"',
                'title="NativePHP on YouTube"',
                'title="NativePHP on Twitch"',
                'title="NativePHP on /dev/null"',
                'title="NativePHP on X"',
                'title="NativePHP on Bluesky"',
                'title="NativePHP on Threads"',
                'title="NativePHP on Facebook"',
                'title="NativePHP on Instagram"',
                'title="NativePHP on TikTok"',
                'title="NativePHP on LinkedIn"',
            ], false);
    }

    #[Test]
    public function social_links_only_appear_in_the_footer_and_not_the_header_menu(): void
    {
        $response = $this
            ->withoutVite()
            ->get('/contributing')
            ->assertOk()
            ->assertSee('aria-label="Social networks"', false)
            ->assertDontSee('aria-label="Social media"', false);

        $content = $response->getContent();

        foreach ([
            'https://x.com/nativephp',
            'https://www.twitch.tv/nativephp',
            'https://www.tiktok.com/@nativephp',
            'https://www.instagram.com/nativephp/',
            'https://www.threads.com/@nativephp',
            'https://www.facebook.com/profile.php?id=61578672170369',
            'https://dev-null.sh/@nativephp',
            'https://www.linkedin.com/company/nativephp/',
        ] as $url) {
            $this->assertSame(1, substr_count($content, 'href="'.$url.'"'), "Expected {$url} to be linked exactly once.");
        }
    }
}
