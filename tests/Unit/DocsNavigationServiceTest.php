<?php

namespace Tests\Unit;

use App\Enums\DocsPlatform;
use App\Services\DocsNavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsNavigationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DocsNavigationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DocsNavigationService::class);
    }

    #[Test]
    public function it_sorts_top_level_entries_by_order(): void
    {
        $navigation = $this->service->build(DocsPlatform::Mobile, 4);

        $orders = array_column($navigation, 'order');

        $this->assertSame($orders, collect($orders)->sort()->values()->all());
    }

    #[Test]
    public function it_builds_a_third_tier_subsection_for_plugins_core(): void
    {
        $navigation = $this->service->build(DocsPlatform::Mobile, 4);

        $plugins = collect($navigation)->firstWhere('relative_path', 'plugins');
        $this->assertNotNull($plugins);

        $core = collect($plugins['children'])->first(fn (array $child) => ($child['is_subsection'] ?? false) && $child['slug'] === 'core');
        $this->assertNotNull($core, 'Expected a "core" subsection under plugins');
        $this->assertSame('Core Plugins', $core['title']);
        $this->assertNotEmpty($core['children']);
        $this->assertContains('Camera', array_column($core['children'], 'title'));
    }

    #[Test]
    public function it_ranks_a_nested_subsection_right_after_its_parent(): void
    {
        $ranks = $this->service->sectionRanks(DocsPlatform::Mobile, 4);

        $this->assertArrayHasKey('plugins', $ranks);
        $this->assertArrayHasKey('plugins/core', $ranks);
        $this->assertGreaterThan($ranks['plugins'], $ranks['plugins/core']);

        // Nothing else should rank between a section and its own subsection.
        $otherRanks = collect($ranks)->except(['plugins', 'plugins/core']);
        $this->assertTrue($otherRanks->every(
            fn (int $rank) => $rank < $ranks['plugins'] || $rank > $ranks['plugins/core']
        ));
    }

    #[Test]
    public function it_returns_no_rank_for_a_section_without_an_index_file(): void
    {
        $ranks = $this->service->sectionRanks(DocsPlatform::Desktop, 2);

        // Desktop v2 has no nested (3rd-tier) subsections today.
        $this->assertTrue(collect($ranks)->keys()->every(fn (string $slug) => ! str_contains($slug, '/')));
    }
}
