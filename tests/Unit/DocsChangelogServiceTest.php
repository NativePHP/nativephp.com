<?php

namespace Tests\Unit;

use App\Enums\DocsPlatform;
use App\Services\DocsChangelogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
