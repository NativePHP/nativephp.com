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
}
