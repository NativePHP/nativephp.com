<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsSearchMetaTagsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function docs_page_includes_docsearch_platform_and_version_meta_tags_for_mobile(): void
    {
        $this
            ->withoutVite()
            ->get('/docs/mobile/3/getting-started/introduction')
            ->assertStatus(200)
            ->assertSee('<meta name="docsearch:platform" content="mobile" />', false)
            ->assertSee('<meta name="docsearch:version" content="3" />', false);
    }

    #[Test]
    public function docs_page_includes_docsearch_platform_and_version_meta_tags_for_desktop(): void
    {
        $this
            ->withoutVite()
            ->get('/docs/desktop/2/getting-started/introduction')
            ->assertStatus(200)
            ->assertSee('<meta name="docsearch:platform" content="desktop" />', false)
            ->assertSee('<meta name="docsearch:version" content="2" />', false);
    }

    #[Test]
    public function non_docs_page_does_not_include_docsearch_meta_tags(): void
    {
        $this
            ->withoutVite()
            ->get('/')
            ->assertStatus(200)
            ->assertDontSee('docsearch:platform', false)
            ->assertDontSee('docsearch:version', false);
    }
}
