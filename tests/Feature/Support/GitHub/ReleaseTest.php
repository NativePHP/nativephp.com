<?php

namespace Tests\Feature\Support\GitHub;

use App\Support\GitHub\Release;
use Tests\TestCase;

class ReleaseTest extends TestCase
{
    public function test_it_converts_at_mentions_to_github_user_links(): void
    {
        $release = new Release([
            'body' => 'Thanks to @simonhamp for the contribution.',
        ]);

        $this->assertStringContainsString(
            '[@simonhamp](https://github.com/simonhamp)',
            $release->getBodyForMarkdown(),
        );
    }

    public function test_it_converts_at_mentions_containing_hyphens_to_github_user_links(): void
    {
        $release = new Release([
            'body' => 'Shout out to @some-user and @a-b-c for shipping this.',
        ]);

        $output = $release->getBodyForMarkdown();

        $this->assertStringContainsString('[@some-user](https://github.com/some-user)', $output);
        $this->assertStringContainsString('[@a-b-c](https://github.com/a-b-c)', $output);
    }

    public function test_it_does_not_match_trailing_hyphens_in_at_mentions(): void
    {
        $release = new Release([
            'body' => 'Mentioning @foo- bar',
        ]);

        $this->assertStringContainsString('[@foo](https://github.com/foo)- bar', $release->getBodyForMarkdown());
    }

    public function test_it_leaves_existing_markdown_links_untouched(): void
    {
        $release = new Release([
            'body' => '* [SuperNative](https://nativephp.com/docs/mobile/4/architecture/super-native) — fully native UI',
        ]);

        $output = $release->getBodyForMarkdown();

        $this->assertStringContainsString(
            '[SuperNative](https://nativephp.com/docs/mobile/4/architecture/super-native)',
            $output,
        );
        $this->assertStringNotContainsString('[[', $output);
        $this->assertStringNotContainsString('([', $output);
        $this->assertStringNotContainsString(')]', $output);
    }

    public function test_it_leaves_pull_request_urls_inside_markdown_links_untouched(): void
    {
        $release = new Release([
            'body' => 'See [the PR](https://github.com/NativePHP/mobile-air/pull/158) for details.',
        ]);

        $this->assertStringContainsString(
            '[the PR](https://github.com/NativePHP/mobile-air/pull/158)',
            $release->getBodyForMarkdown(),
        );
    }

    public function test_it_converts_bare_pull_request_urls(): void
    {
        $release = new Release([
            'body' => '* Fix: Remove stray tag by @martin-ro in https://github.com/NativePHP/mobile-air/pull/158',
        ]);

        $output = $release->getBodyForMarkdown();

        $this->assertStringContainsString('[#158](https://github.com/NativePHP/mobile-air/pull/158)', $output);
        $this->assertStringContainsString('[@martin-ro](https://github.com/martin-ro)', $output);
    }

    public function test_it_converts_other_bare_urls(): void
    {
        $release = new Release([
            'body' => '**Full Changelog**: https://github.com/NativePHP/mobile-air/compare/3.3.6...4.0.0-rc.1',
        ]);

        $this->assertStringContainsString(
            '[https://github.com/NativePHP/mobile-air/compare/3.3.6...4.0.0-rc.1](https://github.com/NativePHP/mobile-air/compare/3.3.6...4.0.0-rc.1)',
            $release->getBodyForMarkdown(),
        );
    }

    public function test_it_does_not_relink_usernames_that_are_already_link_text(): void
    {
        $release = new Release([
            'body' => 'Thanks to [@simonhamp](https://github.com/simonhamp) for shipping this.',
        ]);

        $output = $release->getBodyForMarkdown();

        $this->assertStringContainsString('[@simonhamp](https://github.com/simonhamp)', $output);
        $this->assertStringNotContainsString('[[', $output);
    }

    public function test_it_does_not_convert_email_addresses_to_user_links(): void
    {
        $release = new Release([
            'body' => 'Questions? Email support@nativephp.com any time.',
        ]);

        $this->assertStringContainsString(
            'support@nativephp.com',
            $release->getBodyForMarkdown(),
        );
        $this->assertStringNotContainsString('[@nativephp]', $release->getBodyForMarkdown());
    }
}
