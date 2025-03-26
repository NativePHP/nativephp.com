<?php

namespace App\Support\GitHub;

/**
 * @property string $url
 * @property string $assets_url
 * @property string $upload_url
 * @property string $html_url
 * @property int $id
 * @property array $author
 * @property string $node_id
 * @property string $tag_name
 * @property string $target_commitish
 * @property string $name
 * @property bool $draft
 * @property bool $prerelease
 * @property string $created_at
 * @property string $published_at
 * @property array $assets
 * @property string $tarball_url
 * @property string $zipball_url
 * @property string $body
 * @property int $mentions_count
 */
class Release
{
    private bool $withUserLinks = true;

    private bool $convertLinks = true;

    public function __construct(
        private array $data
    ) {
        //
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function getBodyForMarkdown(): string
    {
        $body = $this->body;

        // Convert any URLs to Markdown links
        if ($this->convertLinks) {
            $body = preg_replace(
                '/https?:\/\/[^\s]+\/pull\/(\d+)/',
                '[#$1]($0)',
                $body
            );

            $body = preg_replace(
                '/(https?:\/\/(?![^\s]+\/pull\/\d+)[^\s]+)/',
                '[$1]($1)',
                $body
            );
        }

        // Change any @ tags to markdown links to GitHub
        if ($this->withUserLinks) {
            $body = preg_replace(
                '/@([a-zA-Z0-9_]+)/',
                '[@$1](https://github.com/$1)',
                $body
            );
        }

        return preg_replace('/^#/m', '##', $body);
    }

    public function withoutUserLinks(bool $withoutUserLinks = true): static
    {
        $this->withUserLinks = ! $withoutUserLinks;

        return $this;
    }

    public function withoutLinkConversion(bool $convertLinks = true): static
    {
        $this->convertLinks = ! $convertLinks;

        return $this;
    }
}
