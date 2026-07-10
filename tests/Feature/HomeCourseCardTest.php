<?php

namespace Tests\Feature;

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
}
