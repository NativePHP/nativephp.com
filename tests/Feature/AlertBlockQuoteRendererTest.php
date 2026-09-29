<?php

namespace Tests\Feature;

use App\Support\CommonMark\CommonMark;
use Tests\TestCase;

class AlertBlockQuoteRendererTest extends TestCase
{
    public function test_note_alert_renders_with_correct_class_and_title(): void
    {
        $html = CommonMark::convertToHtml("> [!NOTE]\n> This is a note.");

        $this->assertStringContainsString('class="markdown-alert markdown-alert-note"', $html);
        $this->assertStringContainsString('class="markdown-alert-title"', $html);
        $this->assertStringContainsString('Note', $html);
        $this->assertStringContainsString('This is a note.', $html);
        $this->assertStringNotContainsString('[!NOTE]', $html);
    }

    public function test_tip_alert_renders_with_correct_class_and_title(): void
    {
        $html = CommonMark::convertToHtml("> [!TIP]\n> This is a tip.");

        $this->assertStringContainsString('markdown-alert-tip', $html);
        $this->assertStringContainsString('Tip', $html);
        $this->assertStringContainsString('This is a tip.', $html);
    }

    public function test_important_alert_renders_with_correct_class_and_title(): void
    {
        $html = CommonMark::convertToHtml("> [!IMPORTANT]\n> This is important.");

        $this->assertStringContainsString('markdown-alert-important', $html);
        $this->assertStringContainsString('Important', $html);
        $this->assertStringContainsString('This is important.', $html);
    }

    public function test_warning_alert_renders_with_correct_class_and_title(): void
    {
        $html = CommonMark::convertToHtml("> [!WARNING]\n> This is a warning.");

        $this->assertStringContainsString('markdown-alert-warning', $html);
        $this->assertStringContainsString('Warning', $html);
        $this->assertStringContainsString('This is a warning.', $html);
    }

    public function test_caution_alert_renders_with_correct_class_and_title(): void
    {
        $html = CommonMark::convertToHtml("> [!CAUTION]\n> This is a caution.");

        $this->assertStringContainsString('markdown-alert-caution', $html);
        $this->assertStringContainsString('Caution', $html);
        $this->assertStringContainsString('This is a caution.', $html);
    }

    public function test_regular_blockquote_still_renders_as_blockquote(): void
    {
        $html = CommonMark::convertToHtml('> This is a regular quote.');

        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringNotContainsString('markdown-alert', $html);
    }

    public function test_multi_paragraph_alert_content(): void
    {
        $markdown = "> [!NOTE]\n> First paragraph.\n>\n> Second paragraph.";
        $html = CommonMark::convertToHtml($markdown);

        $this->assertStringContainsString('markdown-alert-note', $html);
        $this->assertStringContainsString('First paragraph.', $html);
        $this->assertStringContainsString('Second paragraph.', $html);
    }

    public function test_inline_formatting_within_alert(): void
    {
        $markdown = "> [!TIP]\n> Use **bold** and `code` and [links](https://example.com).";
        $html = CommonMark::convertToHtml($markdown);

        $this->assertStringContainsString('markdown-alert-tip', $html);
        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<code>code</code>', $html);
        $this->assertStringContainsString('href="https://example.com"', $html);
    }

    public function test_lowercase_type_does_not_trigger_alert(): void
    {
        $html = CommonMark::convertToHtml("> [!note]\n> This should not be an alert.");

        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringNotContainsString('markdown-alert', $html);
    }

    public function test_invalid_type_does_not_trigger_alert(): void
    {
        $html = CommonMark::convertToHtml("> [!DANGER]\n> This should not be an alert.");

        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringNotContainsString('markdown-alert', $html);
    }

    public function test_alert_contains_svg_icon(): void
    {
        $html = CommonMark::convertToHtml("> [!NOTE]\n> Content here.");

        $this->assertStringContainsString('<svg', $html);
    }

    public function test_alert_with_text_on_same_line_as_marker(): void
    {
        $html = CommonMark::convertToHtml('> [!WARNING] Immediate text on same line.');

        $this->assertStringContainsString('markdown-alert-warning', $html);
        $this->assertStringNotContainsString('[!WARNING]', $html);
    }
}
