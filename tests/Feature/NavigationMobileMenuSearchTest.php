<?php

namespace Tests\Feature;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The menu is a popover, so the browser draws it above everything else,
 * including the DocSearch modal. It has to close whenever search opens,
 * otherwise it sits on top of the search box.
 */
class NavigationMobileMenuSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_closes_when_its_search_button_is_clicked(): void
    {
        $searchButtonContainer = $this->elementOnHomePage('//*[@id="docsearch-desktop"]');

        $this->assertStringContainsString(
            'showMobileMenu = false',
            $searchButtonContainer->getAttribute('x-on:click'),
        );
    }

    public function test_menu_closes_when_a_keyboard_shortcut_opens_search(): void
    {
        $menu = $this->elementOnHomePage('//*[@id="mobile-menu-popover"]/..');

        $this->assertSame('showMobileMenu = false', $menu->getAttribute('x-on:docsearch:open.window'));

        $this->assertMatchesRegularExpression(
            "/onOpen: .*new CustomEvent\('docsearch:open'\)/",
            file_get_contents(resource_path('js/app.js')),
            'DocSearch should announce that it opened with the event the menu listens for.',
        );
    }

    private function elementOnHomePage(string $xpath): DOMElement
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$this->get('/')->assertOk()->getContent());
        libxml_clear_errors();

        $element = (new DOMXPath($dom))->query($xpath)->item(0);

        $this->assertInstanceOf(DOMElement::class, $element, "Nothing on the home page matches {$xpath}.");

        return $element;
    }
}
