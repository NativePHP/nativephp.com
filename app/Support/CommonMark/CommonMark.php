<?php

namespace App\Support\CommonMark;

use App\Extensions\TorchlightWithCopyExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\Embed\Embed as EmbedNode;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\Embed\EmbedRenderer;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Renderer\HtmlDecorator;
use Torchlight\Commonmark\V2\TorchlightExtension;

class CommonMark
{
    protected static ?MarkdownConverter $converter = null;

    public static function convertToHtml(string $markdown, array $data = []): string
    {
        // Pre-process to render any Blade components in the markdown
        $markdown = BladeMarkdownPreprocessor::process($markdown, $data);

        return static::getConverter()->convert($markdown)->getContent();
    }

    protected static function getConverter(): MarkdownConverter
    {
        if (static::$converter === null) {
            $config = [
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 10,
                'embed' => [
                    'adapter' => new OscaroteroEmbedAdapter,
                    'fallback' => 'link',
                ],
            ];

            $environment = new Environment($config);

            $environment->addExtension(new CommonMarkCoreExtension);
            $environment->addExtension(new GithubFlavoredMarkdownExtension);
            $environment->addRenderer(Heading::class, new HeadingRenderer);
            $environment->addExtension(new TableExtension);

            $environment->addExtension(new EmbedExtension);

            $environment->addRenderer(
                EmbedNode::class,
                new HtmlDecorator(new EmbedRenderer, 'div', ['class' => 'relative aspect-video w-full'])
            );

            // Add Torchlight extension if available
            if (class_exists(TorchlightExtension::class)) {
                $environment->addExtension(new TorchlightWithCopyExtension);
            }

            static::$converter = new MarkdownConverter($environment);
        }

        return static::$converter;
    }
}
