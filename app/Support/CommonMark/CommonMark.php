<?php

namespace App\Support\CommonMark;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\Table\TableExtension;
use Torchlight\Commonmark\V2\TorchlightExtension;

class CommonMark
{
    public static function convertToHtml(string $markdown): string
    {
        $converter = new CommonMarkConverter();
        $converter->getEnvironment()->addRenderer(Heading::class, new HeadingRenderer());
        $converter->getEnvironment()->addExtension(new TableExtension());
        $converter->getEnvironment()->addExtension(new TorchlightExtension());

        return $converter->convert($markdown);
    }
}
