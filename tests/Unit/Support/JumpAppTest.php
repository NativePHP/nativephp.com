<?php

namespace Tests\Unit\Support;

use App\Support\JumpApp;
use Tests\TestCase;

class JumpAppTest extends TestCase
{
    public function test_docs_deep_link_uses_the_canonical_domain(): void
    {
        $link = JumpApp::docsDeepLink('docs/mobile/4/edge-components/stack');

        $this->assertSame(
            'https://nativephp.com/docs/mobile/4/edge-components/stack?jump=qr',
            $link
        );
    }

    public function test_docs_deep_link_tolerates_a_leading_slash(): void
    {
        $link = JumpApp::docsDeepLink('/docs/mobile/4/edge-components/badge');

        $this->assertSame(
            'https://nativephp.com/docs/mobile/4/edge-components/badge?jump=qr',
            $link
        );
    }

    public function test_docs_deep_link_ignores_the_configured_app_url(): void
    {
        config(['app.url' => 'https://keen-pony.test']);

        $this->assertStringStartsWith(
            'https://nativephp.com/',
            JumpApp::docsDeepLink('docs/mobile/4/edge-components/stack')
        );
    }
}
