<?php

namespace Tests\Feature\Docs;

use App\Services\DocsSearchService;
use App\Support\CommonMark\CommonMark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class VersionBadgeTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_package_since_renders_the_package_and_version(): void
    {
        $this->blade('<x-docs.version-badge package="mobile-ui" since="0.6" />')
            ->assertSee('>mobile-ui 0.6<', false)
            ->assertSee('title="Added in nativephp/mobile-ui 0.6"', false);
    }

    public function test_package_changed_renders_with_prefix(): void
    {
        $this->blade('<x-docs.version-badge package="mobile-ui" changed="0.6" />')
            ->assertSee('>Changed mobile-ui 0.6<', false)
            ->assertSee('title="Changed in nativephp/mobile-ui 0.6"', false);
    }

    public function test_package_deprecated_renders_with_prefix(): void
    {
        $this->blade('<x-docs.version-badge package="mobile-ui" deprecated="0.6" />')
            ->assertSee('>Deprecated mobile-ui 0.6<', false);
    }

    public function test_package_removed_renders_with_prefix(): void
    {
        $this->blade('<x-docs.version-badge package="mobile-ui" removed="0.6" />')
            ->assertSee('>Removed mobile-ui 0.6<', false);
    }

    public function test_package_labels_at_or_below_the_baseline_render_nothing(): void
    {
        // mobile-ui 0.3 was current when Mobile 4.0 shipped, so like x.0 it's
        // the starting point rather than something to label.
        $this->blade('<x-docs.version-badge package="mobile-ui" since="0.3" />')
            ->assertDontSee('0.3');

        $this->blade('<x-docs.version-badge package="mobile-ui" since="0.1" />')
            ->assertDontSee('0.1');
    }

    public function test_an_unknown_package_renders_nothing(): void
    {
        $this->blade('<x-docs.version-badge package="mobile-uix" since="0.6" />')
            ->assertDontSee('0.6');
    }

    public function test_a_badge_renders_on_one_line(): void
    {
        // Labels sit inline in markdown, including inside table cells, where a
        // single newline in the rendered HTML ends the row and collapses the
        // rest of the table into paragraph text.
        $linked = (string) $this->blade('<x-docs.badge label="4.2" tooltip="Added in NativePHP 4.2" href="/docs" />');
        $bare = (string) $this->blade('<x-docs.version-badge since="4.2" />');
        $package = (string) $this->blade('<x-docs.version-badge package="mobile-ui" since="0.6" />');

        foreach ([$linked, $bare, $package] as $badge) {
            $this->assertStringNotContainsString("\n", $badge);
            $this->assertSame(trim($badge), $badge);
        }
    }

    public function test_an_inline_label_leaves_its_table_row_intact(): void
    {
        $html = CommonMark::convertToHtml(
            "| Utility | Classes |\n| --- | --- |\n| Rounded <x-docs.version-badge since=\"4.2\" /> | `rounded-full` |\n"
        );

        $this->assertSame(2, substr_count($html, '<td>'));
        $this->assertStringContainsString('<code>rounded-full</code></td>', $html);

        preg_match('/<td>(.*?)<\/td>/s', $html, $firstCell);

        $this->assertStringContainsString('4.2', $firstCell[1]);
        $this->assertStringNotContainsString("\n", $firstCell[1]);
    }

    public function test_layout_page_contains_the_4_2_pill(): void
    {
        $this->get('/docs/mobile/4/edge-components/layout')
            ->assertStatus(200)
            ->assertSee('4.2');
    }

    public function test_a_package_label_links_to_its_section_of_the_versioning_page(): void
    {
        $response = $this->get('/docs/mobile/4/getting-started/versioning')
            ->assertStatus(200)
            ->assertSee('id="mobile-ui-labels"', false);

        $this->assertMatchesRegularExpression(
            '/<a href="[^"]*\/getting-started\/versioning#mobile-ui-labels"[^>]*>mobile-ui 0\.6<\/a>/',
            $response->getContent()
        );
    }

    public function test_accordion_page_carries_a_page_level_mobile_ui_label(): void
    {
        $this->get('/docs/mobile/4/edge-components/accordion')
            ->assertStatus(200)
            ->assertSee('>mobile-ui 0.4<', false);
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
        $packages = config('docs.packages');
        $finder = (new Finder)->files()->name('*.md')->in(resource_path('views/docs'));

        $violations = [];

        foreach ($finder as $file) {
            $relative = $file->getRelativePathname();
            $parts = explode(DIRECTORY_SEPARATOR, $relative);

            if (count($parts) < 2 || ! is_numeric($parts[1])) {
                continue;
            }

            [$platform, $major] = [$parts[0], (int) $parts[1]];

            // A package label is checked against the package's own releases.
            // Null means the package isn't in config('docs.packages').
            $releasedFor = fn (?string $package): ?array => $package === null
                ? ($releasedVersions[$platform][$major] ?? [])
                : ($packages[$package]['released_versions'] ?? null);

            $content = $file->getContents();
            $document = YamlFrontMatter::parse($content);

            $package = $document->matter('package');
            $packageNote = $package === null ? '' : " `package: {$package}`";
            $allowed = $releasedFor($package);

            if ($allowed === null) {
                $violations[] = "{$relative} front matter `package: {$package}` isn't in config('docs.packages')";
            }

            foreach (['since', 'changed', 'deprecated', 'removed'] as $key) {
                $value = $document->matter($key);

                if ($value === null) {
                    continue;
                }

                // YAML reads an unquoted `since: 0.10` as the float 0.1, so
                // the label would quietly show the wrong version.
                if (! is_string($value)) {
                    $violations[] = "{$relative} front matter `{$key}: ".json_encode($value)."` must be quoted, e.g. `{$key}: \"0.10\"`, or YAML reads it as a number";
                } elseif ($allowed !== null && ! in_array($value, $allowed, true)) {
                    $violations[] = "{$relative} front matter{$packageNote} `{$key}: {$value}`";
                }
            }

            $jump = $document->matter('jump');

            if ($jump !== null && $jump !== false && ! is_string($jump)) {
                $violations[] = "{$relative} front matter `jump: ".json_encode($jump).'` must be a quoted version, e.g. `jump: "3.10"`, or false';
            }

            if (preg_match_all('/<x-docs\.version-badge([^>]*)\/>/s', $content, $tagMatches)) {
                foreach ($tagMatches[1] as $attrs) {
                    $package = preg_match('/package="([^"]+)"/', $attrs, $m) ? $m[1] : null;
                    $packageAttribute = $package === null ? '' : " package=\"{$package}\"";
                    $allowed = $releasedFor($package);

                    if ($allowed === null) {
                        $violations[] = "{$relative} <x-docs.version-badge{$packageAttribute} /> isn't in config('docs.packages')";

                        continue;
                    }

                    foreach (['since', 'changed', 'deprecated', 'removed'] as $key) {
                        if (preg_match('/'.$key.'="([^"]+)"/', $attrs, $m)) {
                            if (! in_array($m[1], $allowed, true)) {
                                $violations[] = "{$relative} <x-docs.version-badge{$packageAttribute} {$key}=\"{$m[1]}\" />";
                            }
                            break;
                        }
                    }
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "Version labels that are unquoted, name an unknown package, or point at an unreleased or mismatched version:\n".implode("\n", $violations)
        );
    }

    public function test_every_package_baseline_is_one_of_its_released_versions(): void
    {
        $packages = config('docs.packages');

        $this->assertNotEmpty($packages);

        foreach ($packages as $slug => $package) {
            $this->assertContains(
                $package['baseline'],
                $package['released_versions'],
                "config('docs.packages.{$slug}.baseline') isn't one of its released_versions"
            );
        }
    }
}
