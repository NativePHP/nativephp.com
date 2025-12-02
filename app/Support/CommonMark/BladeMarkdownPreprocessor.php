<?php

namespace App\Support\CommonMark;

use Illuminate\Support\Facades\Blade;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Torchlight\Commonmark\V2\TorchlightExtension;

class BladeMarkdownPreprocessor
{
    /**
     * Process markdown content to render Blade components before markdown parsing.
     *
     * This extracts code blocks first (to prevent them from being processed),
     * renders any Blade components in the remaining content, then restores
     * the code blocks. Code blocks that end up inside HTML elements are
     * pre-converted to HTML to ensure proper syntax highlighting.
     */
    public static function process(string $markdown, array $data = []): string
    {
        $codeBlocks = [];
        $placeholder = '___CODE_BLOCK_PLACEHOLDER_%d___';

        // Extract fenced code blocks (``` or ~~~)
        $markdown = preg_replace_callback(
            '/^(`{3,}|~{3,})([^\n]*)\n(.*?)\n\1/ms',
            function ($matches) use (&$codeBlocks, $placeholder) {
                $index = count($codeBlocks);
                $codeBlocks[$index] = $matches[0];

                return sprintf($placeholder, $index);
            },
            $markdown
        );

        // Extract inline code (single backticks)
        $inlineCode = [];
        $inlinePlaceholder = '___INLINE_CODE_PLACEHOLDER_%d___';
        $markdown = preg_replace_callback(
            '/`[^`]+`/',
            function ($matches) use (&$inlineCode, $inlinePlaceholder) {
                $index = count($inlineCode);
                $inlineCode[$index] = $matches[0];

                return sprintf($inlinePlaceholder, $index);
            },
            $markdown
        );

        // Check if there are any Blade components to process
        if (static::containsBladeComponents($markdown)) {
            // Render the content as a Blade string
            $markdown = static::renderBladeContent($markdown, $data);
        }

        // Restore inline code
        foreach ($inlineCode as $index => $code) {
            $markdown = str_replace(sprintf($inlinePlaceholder, $index), $code, $markdown);
        }

        // Restore code blocks - convert to HTML if inside HTML elements
        foreach ($codeBlocks as $index => $block) {
            $placeholderStr = sprintf($placeholder, $index);

            // Check if placeholder is inside an HTML element (after Blade rendering)
            if (static::isInsideHtmlElement($markdown, $placeholderStr)) {
                // Convert code block to HTML before restoring
                $block = static::convertCodeBlockToHtml($block);
            }

            $markdown = str_replace($placeholderStr, $block, $markdown);
        }

        return $markdown;
    }

    /**
     * Check if a placeholder is inside an HTML element.
     */
    protected static function isInsideHtmlElement(string $content, string $placeholder): bool
    {
        $pos = strpos($content, $placeholder);
        if ($pos === false) {
            return false;
        }

        // Get content before placeholder and count open/close tags
        $before = substr($content, 0, $pos);

        // Simple heuristic: check if there's an unclosed HTML tag before the placeholder
        // Look for patterns like <div...> that aren't closed
        $openTags = preg_match_all('/<(div|span|section|article|aside|p)[^>]*>(?!.*<\/\1>)/i', $before);
        $lastOpenTag = strrpos($before, '<');
        $lastCloseTag = strrpos($before, '>');

        // If the last < is after the last >, we're likely inside a tag (shouldn't happen with placeholders)
        // More reliable: check if there's an HTML structure pattern before the placeholder
        return (bool) preg_match('/<[a-z][^>]*>\s*$/i', $before);
    }

    /**
     * Convert a markdown code block to HTML using Torchlight.
     */
    protected static function convertCodeBlockToHtml(string $codeBlock): string
    {
        try {
            $config = [
                'html_input' => 'allow',
            ];

            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension);

            if (class_exists(TorchlightExtension::class)) {
                $environment->addExtension(new TorchlightExtension);
            }

            $converter = new MarkdownConverter($environment);

            return trim($converter->convert($codeBlock)->getContent());
        } catch (\Throwable $e) {
            report($e);

            return $codeBlock;
        }
    }

    /**
     * Check if the content contains Blade components.
     */
    protected static function containsBladeComponents(string $content): bool
    {
        // Look for <x-*> component tags or Blade directives/echoes
        return (bool) preg_match('/<x-[\w\-\.:]+|@[\w]+|{{\s*|{!!\s*/', $content);
    }

    /**
     * Render Blade content, handling components and directives.
     */
    protected static function renderBladeContent(string $content, array $data = []): string
    {
        try {
            return Blade::render($content, $data);
        } catch (\Throwable $e) {
            // If Blade rendering fails, return original content
            // This can happen if the content has invalid Blade syntax
            report($e);

            return $content;
        }
    }
}
