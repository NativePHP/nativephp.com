<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NavigationBarTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_bifrost_button_just_says_bifrost(): void
    {
        foreach (['<x-bifrost-button />', '<x-bifrost-button small />'] as $template) {
            $button = $this->blade($template);

            $this->assertSame('Bifrost', trim(strip_tags((string) $button)));

            $button->assertDontSee('gsap', escape: false);
        }
    }

    /**
     * Ultra, Masterclass and Build used to be tinted, see-through pills that
     * lost their contrast whenever the sticky nav floated over dark content.
     * They now share the resting background of the Mobile and Desktop dropdown
     * buttons, so the expected classes are read off the Mobile dropdown itself.
     */
    #[Test]
    public function the_desktop_nav_links_share_the_device_dropdown_background(): void
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$this->get('/')->assertOk()->getContent());
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $dropdownClasses = $xpath->query('//button[@id="mobile-dropdown-btn"]')->item(0)?->getAttribute(':class');

        $this->assertSame(
            1,
            preg_match("/'([^']+)':\s*!open/", (string) $dropdownClasses, $closedDropdown),
            'Could not read the resting background of the Mobile dropdown button.',
        );

        $expectedClasses = preg_split('/\s+/', $closedDropdown[1], flags: PREG_SPLIT_NO_EMPTY);

        $links = [
            'Ultra' => route('pricing'),
            'Masterclass' => route('course'),
            'Build' => route('build-my-app'),
        ];

        foreach ($links as $label => $href) {
            $link = $xpath->query("//nav[@data-site-nav]/div/div/a[@href='{$href}']")->item(0);

            $this->assertNotNull($link, "The {$label} link is missing from the nav bar.");
            $this->assertSame($label, trim($link->textContent));

            $linkClasses = preg_split('/\s+/', $link->getAttribute('class'), flags: PREG_SPLIT_NO_EMPTY);

            $this->assertSame(
                [],
                array_values(array_diff($expectedClasses, $linkClasses)),
                "The {$label} link should use the same background as the Mobile and Desktop dropdowns.",
            );
        }
    }
}
