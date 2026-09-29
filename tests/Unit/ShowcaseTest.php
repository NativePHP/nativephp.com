<?php

namespace Tests\Unit;

use App\Models\Showcase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ShowcaseTest extends TestCase
{
    #[Test]
    public function screenshots_for_falls_back_to_shared_screenshots_without_a_platform(): void
    {
        $showcase = new Showcase(['screenshots' => ['shared-1.png', 'shared-2.png']]);

        $this->assertSame(['shared-1.png', 'shared-2.png'], $showcase->screenshotsFor(null));
    }

    #[Test]
    public function screenshots_for_falls_back_to_shared_screenshots_when_no_override_exists(): void
    {
        $showcase = new Showcase(['screenshots' => ['shared-1.png']]);

        $this->assertSame(['shared-1.png'], $showcase->screenshotsFor('mobile'));
        $this->assertSame(['shared-1.png'], $showcase->screenshotsFor('desktop'));
    }

    #[Test]
    public function screenshots_for_falls_back_to_shared_screenshots_when_override_is_empty(): void
    {
        $showcase = new Showcase(['screenshots' => ['shared-1.png'], 'mobile_screenshots' => []]);

        $this->assertSame(['shared-1.png'], $showcase->screenshotsFor('mobile'));
    }

    #[Test]
    public function screenshots_for_returns_the_mobile_override_when_present(): void
    {
        $showcase = new Showcase([
            'screenshots' => ['shared-1.png'],
            'mobile_screenshots' => ['mobile-1.png'],
            'desktop_screenshots' => ['desktop-1.png'],
        ]);

        $this->assertSame(['mobile-1.png'], $showcase->screenshotsFor('mobile'));
    }

    #[Test]
    public function screenshots_for_returns_the_desktop_override_when_present(): void
    {
        $showcase = new Showcase([
            'screenshots' => ['shared-1.png'],
            'mobile_screenshots' => ['mobile-1.png'],
            'desktop_screenshots' => ['desktop-1.png'],
        ]);

        $this->assertSame(['desktop-1.png'], $showcase->screenshotsFor('desktop'));
    }

    #[Test]
    public function screenshots_for_returns_an_empty_array_when_nothing_is_set(): void
    {
        $showcase = new Showcase;

        $this->assertSame([], $showcase->screenshotsFor(null));
        $this->assertSame([], $showcase->screenshotsFor('mobile'));
    }
}
