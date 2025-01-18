<?php

namespace App\Support\CommonMark;

use Illuminate\Support\Str;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class HeadingRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        $tag = 'h'.$node->getLevel();

        $attrs = $node->data->get('attributes', []);

        $element = new HtmlElement($tag, $attrs, $childRenderer->renderNodes($node->children()));

        $id = Str::slug($element->getContents());

        $element->setAttribute('id', $id);

        if ($node->getLevel() === 1 || $node->getLevel() === 2 || $node->getLevel() === 3) {
            $element->setContents(
                new HtmlElement(
                    'a',
                    ['href' => "#{$id}", 'class' => 'mr-2 no-underline font-medium', 'style' => 'border-bottom: 0 !important;'],
                    new HtmlElement('span', ['class' => ' text-gray-600 dark:text-gray-400 hover:text-[#00aaa6]'], '#'),
                ).
                $element->getContents()
            );
        }

        return $element;
    }
}
