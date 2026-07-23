<?php

namespace App\Support;

use App\Models\Article;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Collection;

class BlogFeed
{
    private const string ATOM_NAMESPACE = 'http://www.w3.org/2005/Atom';

    private const string DUBLIN_CORE_NAMESPACE = 'http://purl.org/dc/elements/1.1/';

    private const string MEDIA_NAMESPACE = 'http://search.yahoo.com/mrss/';

    /**
     * @param  Collection<int, Article>  $articles
     */
    public function toRss(Collection $articles): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $rss = $document->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', self::ATOM_NAMESPACE);
        $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dc', self::DUBLIN_CORE_NAMESPACE);
        $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:media', self::MEDIA_NAMESPACE);
        $document->appendChild($rss);

        $channel = $document->createElement('channel');
        $rss->appendChild($channel);

        $this->appendText($document, $channel, 'title', 'NativePHP Blog');
        $this->appendText($document, $channel, 'link', route('blog'));

        $self = $document->createElementNS(self::ATOM_NAMESPACE, 'atom:link');
        $self->setAttribute('href', route('blog.feed'));
        $self->setAttribute('rel', 'self');
        $self->setAttribute('type', 'application/rss+xml');
        $channel->appendChild($self);

        $this->appendText($document, $channel, 'description', 'Insights, updates, and stories from the NativePHP community.');
        $this->appendText($document, $channel, 'language', 'en');

        if ($articles->isNotEmpty()) {
            $this->appendText($document, $channel, 'lastBuildDate', $articles->max('updated_at')->toRssString());
        }

        foreach ($articles as $article) {
            $this->appendItem($document, $channel, $article);
        }

        return (string) $document->saveXML();
    }

    private function appendItem(DOMDocument $document, DOMElement $channel, Article $article): void
    {
        $item = $document->createElement('item');
        $channel->appendChild($item);

        $url = route('article', $article);

        $this->appendText($document, $item, 'title', $article->title);
        $this->appendText($document, $item, 'link', $url);
        $this->appendText($document, $item, 'guid', $url)->setAttribute('isPermaLink', 'true');
        $this->appendText($document, $item, 'pubDate', $article->published_at->toRssString());

        if ($article->author?->name) {
            $creator = $document->createElementNS(self::DUBLIN_CORE_NAMESPACE, 'dc:creator');
            $creator->appendChild($document->createTextNode($article->author->name));
            $item->appendChild($creator);
        }

        $this->appendText($document, $item, 'description', $article->excerpt ?? '');

        if ($article->og_image) {
            $media = $document->createElementNS(self::MEDIA_NAMESPACE, 'media:content');
            $media->setAttribute('url', $article->og_image);
            $media->setAttribute('medium', 'image');
            $item->appendChild($media);
        }
    }

    private function appendText(DOMDocument $document, DOMElement $parent, string $name, string $value): DOMElement
    {
        $element = $document->createElement($name);
        $element->appendChild($document->createTextNode($value));
        $parent->appendChild($element);

        return $element;
    }
}
