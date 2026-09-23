<?php

namespace Tests\Feature;

use App\Support\CommonMark\CommonMark;
use Tests\TestCase;

class HeadingRendererTest extends TestCase
{
    public function test_heading_hash_appears_after_text(): void
    {
        $html = CommonMark::convertToHtml('## My Heading');

        $this->assertStringContainsString('My Heading<a', $html);
        $this->assertStringNotContainsString('#</span></a>My Heading', $html);
    }

    public function test_heading_anchor_has_correct_class(): void
    {
        $html = CommonMark::convertToHtml('## Test Heading');

        $this->assertStringContainsString('heading-anchor', $html);
        $this->assertStringContainsString('ml-2', $html);
    }

    public function test_heading_has_id_attribute(): void
    {
        $html = CommonMark::convertToHtml('## My Section');

        $this->assertStringContainsString('id="my-section"', $html);
    }

    public function test_heading_anchor_links_to_id(): void
    {
        $html = CommonMark::convertToHtml('## My Section');

        $this->assertStringContainsString('href="#my-section"', $html);
    }

    public function test_h1_gets_anchor(): void
    {
        $html = CommonMark::convertToHtml('# Title');

        $this->assertStringContainsString('heading-anchor', $html);
    }

    public function test_h3_gets_anchor(): void
    {
        $html = CommonMark::convertToHtml('### Sub Section');

        $this->assertStringContainsString('heading-anchor', $html);
    }

    public function test_h4_does_not_get_anchor(): void
    {
        $html = CommonMark::convertToHtml('#### Deep Heading');

        $this->assertStringNotContainsString('heading-anchor', $html);
    }

    public function test_duplicate_headings_get_unique_ids(): void
    {
        $html = CommonMark::convertToHtml("## Installation\n\nSome text.\n\n## Installation");

        preg_match_all('/id="([^"]+)"/', $html, $matches);
        $ids = $matches[1];

        $this->assertCount(2, $ids);
        $this->assertCount(2, array_unique($ids), 'Heading IDs should be unique');
        $this->assertSame('installation', $ids[0]);
        $this->assertSame('installation-1', $ids[1]);
    }

    public function test_empty_slug_gets_fallback_id(): void
    {
        // A heading with only special characters that Str::slug strips
        $html = CommonMark::convertToHtml('## !!!');

        $this->assertStringContainsString('id="heading"', $html);
    }

    public function test_ids_reset_between_conversions(): void
    {
        CommonMark::convertToHtml('## Installation');
        $html = CommonMark::convertToHtml('## Installation');

        $this->assertStringContainsString('id="installation"', $html);
        $this->assertStringNotContainsString('id="installation-1"', $html);
    }
}
