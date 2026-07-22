<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeCourseCardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_course_card_badge_reads_video_course_instead_of_early_bird()
    {
        $this->blade('<x-home.course-card />')
            ->assertSee('Video Course')
            ->assertDontSee('Early Bird');
    }

    #[Test]
    public function the_homepage_shows_the_video_course_badge()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Video Course')
            ->assertDontSee('Early Bird');
    }

    /**
     * The announcement cards (Plugins, Masterclass, Jump, Bifrost) are all
     * mobile-specific, so they show on the Mobile track and drop out on
     * Desktop. Asserting on the wrapper rather than the page text, because the
     * cards stay in the DOM either way — only their visibility changes.
     */
    #[Test]
    public function the_announcement_cards_are_scoped_to_the_mobile_track()
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$this->get('/')->getContent());
        libxml_clear_errors();

        $wrapper = (new DOMXPath($dom))
            ->query('//*[@data-platform-section="announcements"]')
            ->item(0);

        $this->assertNotNull($wrapper, 'Announcements wrapper is missing.');

        $this->assertSame(
            "\$store.platform.is('mobile')",
            $wrapper->getAttribute('x-show'),
            'Announcements should only show on the Mobile track.',
        );

        // Guards against the wrapper being correctly bound but empty.
        $contents = $dom->saveHTML($wrapper);

        foreach (['Video Course', 'Cloud Platform', 'Jump'] as $card) {
            $this->assertStringContainsString($card, $contents);
        }
    }
}
