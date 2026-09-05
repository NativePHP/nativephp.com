<?php

namespace Tests\Feature;

use App\Support\CommonMark\CommonMark;
use Tests\TestCase;

class CommonMarkSanitizationTest extends TestCase
{
    public function test_it_neutralizes_a_raw_script_tag(): void
    {
        $html = CommonMark::convertToHtml("Look at this:\n\n<script>alert('xss')</script>");

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script>', $html);
    }

    public function test_it_neutralizes_an_inline_script_tag(): void
    {
        $html = CommonMark::convertToHtml('Inline <script>alert(1)</script> markup.');

        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_it_neutralizes_a_style_tag(): void
    {
        $html = CommonMark::convertToHtml('<style>body { display: none; }</style>');

        $this->assertStringNotContainsString('<style>', $html);
    }

    public function test_it_still_renders_ordinary_markdown_formatting(): void
    {
        $html = CommonMark::convertToHtml('**Fixed** a crash on launch.');

        $this->assertStringContainsString('<strong>Fixed</strong>', $html);
    }
}
