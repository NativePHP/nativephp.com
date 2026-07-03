<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocsPrereleaseVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_prerelease_pages_show_the_beta_notice(): void
    {
        $response = $this->get('/docs/mobile/4/edge-components/button');

        $response->assertOk();
        $response->assertSee('pre-release documentation');
        $response->assertSee('View the stable version (3.x)');
    }

    public function test_beta_notice_links_to_the_same_page_when_it_exists_on_stable(): void
    {
        // top-bar exists in v3 edge-components, so the notice should deep-link to it.
        $this->get('/docs/mobile/4/edge-components/top-bar')
            ->assertOk()
            ->assertSee('/docs/mobile/3/edge-components/top-bar');

        // button does not exist in v3, so the notice falls back to the introduction.
        $this->get('/docs/mobile/4/edge-components/button')
            ->assertOk()
            ->assertSee('/docs/mobile/3/getting-started/introduction');
    }

    public function test_stable_pages_do_not_show_the_beta_notice(): void
    {
        $this->get('/docs/mobile/3/getting-started/introduction')
            ->assertOk()
            ->assertDontSee('pre-release documentation');
    }

    public function test_unversioned_docs_urls_redirect_to_the_stable_version(): void
    {
        $this->get('/docs/mobile')
            ->assertRedirect('/docs/mobile/3/getting-started/introduction');

        $this->get('/docs/mobile/the-basics/web-view')
            ->assertRedirect('/docs/mobile/3/the-basics/web-view');
    }

    public function test_version_switcher_lists_newest_first_and_labels_beta(): void
    {
        $response = $this->get('/docs/mobile/4/getting-started/introduction');
        $content = $response->getContent();

        $response->assertOk();
        $response->assertSee('Version 4.x (beta)');

        // Newest version appears before older ones in the dropdown.
        $this->assertMatchesRegularExpression(
            '#<option value="4">[^<]*</option>\s*<option value="3">#s',
            $content,
        );
    }

    public function test_supernative_shield_badges_are_removed_from_navigation(): void
    {
        $this->get('/docs/mobile/4/edge-components/button')
            ->assertOk()
            ->assertDontSee('lucide-super-native')
            ->assertDontSee('This is a SuperNative feature');
    }
}
