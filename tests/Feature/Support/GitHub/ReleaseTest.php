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
}
