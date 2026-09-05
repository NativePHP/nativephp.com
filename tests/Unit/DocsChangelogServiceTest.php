<?php

namespace Tests\Unit;

use App\Enums\DocsPlatform;
use App\Services\DocsChangelogService;
use App\Support\GitHub\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsChangelogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DocsChangelogService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DocsChangelogService::class);
    }

    #[Test]
    public function it_finds_the_known_since_badge_on_the_layout_page(): void
    {
        $badges = $this->service->badgesForVersion(DocsPlatform::Mobile, 4);

        $this->assertArrayHasKey('4.2', $badges);
        $this->assertArrayHasKey('added', $badges['4.2']);

        $titles = array_column($badges['4.2']['added'], 'title');
        $this->assertContains('Layout & Styling', $titles);
    }

    #[Test]
    public function it_finds_the_known_removed_badge_on_the_system_page(): void
    {
        $badges = $this->service->badgesForVersion(DocsPlatform::Mobile, 4);

        $this->assertArrayHasKey('4.1', $badges);
        $titles = array_column($badges['4.1']['removed'] ?? [], 'title');
        $this->assertContains('System', $titles);
    }

    #[Test]
    public function it_excludes_the_versioning_policy_pages_own_example_badges(): void
    {
        $badges = $this->service->badgesForVersion(DocsPlatform::Mobile, 4);

        foreach ($badges as $types) {
            foreach ($types as $pages) {
                $this->assertNotContains('Versioning Policy', array_column($pages, 'title'));
            }
        }
    }

    #[Test]
    public function it_returns_minor_versions_sorted_descending(): void
    {
        $badges = $this->service->badgesForVersion(DocsPlatform::Mobile, 4);

        $minors = array_keys($badges);
        $sorted = $minors;
        rsort($sorted, SORT_NATURAL);

        $this->assertSame($sorted, $minors);
    }

    #[Test]
    public function it_returns_an_empty_array_when_nothing_is_labelled(): void
    {
        $badges = $this->service->badgesForVersion(DocsPlatform::Desktop, 1);

        $this->assertSame([], $badges);
    }

    #[Test]
    public function it_links_a_minor_version_at_its_latest_patch_release(): void
    {
        $this->seedReleases(['4.2.0', '4.2.10', '4.2.9', '4.1.0']);

        $links = $this->service->changelogLinks(DocsPlatform::Mobile, 4, ['4.2', '4.1']);

        $this->assertSame('4.2.10', $links['4.2']['version']);
        $this->assertSame(
            route('docs.show', ['platform' => 'mobile', 'version' => 4, 'page' => 'getting-started/changelog']).'#4210',
            $links['4.2']['url'],
        );
        $this->assertSame('4.1.0', $links['4.1']['version']);
    }

    #[Test]
    public function it_anchors_on_the_release_name_as_the_changelog_prints_it(): void
    {
        $this->seedReleases(['v4.2.1']);

        $links = $this->service->changelogLinks(DocsPlatform::Mobile, 4, ['4.2']);

        $this->assertStringEndsWith('#v421', $links['4.2']['url']);
        $this->assertSame('v4.2.1', $links['4.2']['version']);
    }

    #[Test]
    public function it_skips_minor_versions_with_no_release_to_point_at(): void
    {
        $this->seedReleases(['4.1.0']);

        $this->assertSame([], $this->service->changelogLinks(DocsPlatform::Mobile, 4, ['4.9']));
    }

    #[Test]
    public function it_returns_no_links_for_a_major_whose_changelog_is_hand_written(): void
    {
        $this->seedReleases(['2.1.0']);

        $this->assertSame([], $this->service->changelogLinks(DocsPlatform::Mobile, 2, ['2.1']));
    }

    /**
     * @param  array<int, string>  $names
     */
    private function seedReleases(array $names, string $repository = 'nativephp/mobile-air'): void
    {
        Cache::put("{$repository}-releases", collect($names)->map(
            fn (string $name) => new Release(['tag_name' => $name, 'name' => $name]),
        ));
    }
}
