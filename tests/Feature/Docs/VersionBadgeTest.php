<?php

namespace Tests\Feature\Docs;

use App\Services\DocsSearchService;
use App\Support\CommonMark\CommonMark;
use Illuminate\Support\Facades\Http;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class VersionBadgeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Full-page renders below hit fenced code blocks; Torchlight throws
        // outside production without a token, so fake its offline fallback.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    public function test_since_renders_bare(): void
    {
        // The tooltip legitimately says "Added in ..." — it's the visible
        // label, sandwiched between tags with no prefix, that must be bare.
        $this->blade('<x-docs.version-badge since="4.2" />')
            ->assertSee('>4.2<', false);
    }

    public function test_x_dot_zero_renders_nothing(): void
    {
        $this->blade('<x-docs.version-badge since="4.0" />')
            ->assertDontSee('4.0');
    }

    public function test_changed_renders_with_prefix(): void
    {
        $this->blade('<x-docs.version-badge changed="4.2" />')
            ->assertSee('Changed 4.2');
    }

    public function test_deprecated_renders_with_prefix(): void
    {
        $this->blade('<x-docs.version-badge deprecated="4.1" />')
            ->assertSee('Deprecated 4.1');
    }

    public function test_removed_renders_with_prefix(): void
    {
        $this->blade('<x-docs.version-badge removed="4.1" />')
            ->assertSee('Removed 4.1');
    }

    public function test_layout_page_contains_the_4_2_pill(): void
    {
        $this->get('/docs/mobile/4/edge-components/layout')
            ->assertStatus(200)
            ->assertSee('4.2');
    }

    public function test_section_label_does_not_change_the_heading_anchor_id(): void
    {
        $html = CommonMark::convertToHtml(
            "## Observing the lifecycle from outside\n\n<x-docs.version-badge since=\"4.1\" />\n\nBody text."
        );

        $this->assertStringContainsString('id="observing-the-lifecycle-from-outside"', $html);
    }

    public function test_lifecycle_hooks_page_keeps_its_heading_anchor(): void
    {
        $this->get('/docs/mobile/4/digging-deeper/lifecycle-hooks')
            ->assertStatus(200)
            ->assertSee('id="observing-the-lifecycle-from-outside"', false);
    }

    public function test_search_index_content_contains_no_badge_markup(): void
    {
        $page = app(DocsSearchService::class)->getPage('mobile', '4', 'edge-components', 'layout');

        $this->assertNotNull($page);
        $this->assertStringNotContainsString('<x-docs', $page['content']);
        $this->assertStringNotContainsString('version-badge', $page['content']);
    }

    public function test_every_version_label_points_at_a_released_version(): void
    {
        $releasedVersions = config('docs.released_versions');
        $finder = (new Finder)->files()->name('*.md')->in(resource_path('views/docs'));

        $violations = [];

        foreach ($finder as $file) {
            $relative = $file->getRelativePathname();
            $parts = explode(DIRECTORY_SEPARATOR, $relative);

            if (count($parts) < 2 || ! is_numeric($parts[1])) {
                continue;
            }

            [$platform, $major] = [$parts[0], (int) $parts[1]];
            $allowed = $releasedVersions[$platform][$major] ?? [];

            $content = $file->getContents();
            $document = YamlFrontMatter::parse($content);

            foreach (['since', 'changed', 'deprecated', 'removed'] as $key) {
                $value = $document->matter($key);

                if ($value !== null && ! in_array((string) $value, $allowed, true)) {
                    $violations[] = "{$relative} front matter `{$key}: {$value}`";
                }
            }

            if (preg_match_all('/<x-docs\.version-badge([^>]*)\/>/s', $content, $tagMatches)) {
                foreach ($tagMatches[1] as $attrs) {
                    foreach (['since', 'changed', 'deprecated', 'removed'] as $key) {
                        if (preg_match('/'.$key.'="([^"]+)"/', $attrs, $m)) {
                            if (! in_array($m[1], $allowed, true)) {
                                $violations[] = "{$relative} <x-docs.version-badge {$key}=\"{$m[1]}\" />";
                            }
                            break;
                        }
                    }
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "Version labels pointing at an unreleased or mismatched version:\n".implode("\n", $violations)
        );
    }
}
