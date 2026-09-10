<?php

namespace Tests\Unit;

use App\Enums\DocsPlatform;
use App\Services\DocsVersionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsVersionRegistryTest extends TestCase
{
    use RefreshDatabase;

    protected DocsVersionRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = app(DocsVersionRegistry::class);
    }

    #[Test]
    public function it_reads_the_latest_version_per_platform(): void
    {
        $this->assertSame(config('docs.latest_versions.mobile'), $this->registry->latest(DocsPlatform::Mobile));
        $this->assertSame(config('docs.latest_versions.desktop'), $this->registry->latest(DocsPlatform::Desktop));
    }

    #[Test]
    public function it_reports_prerelease_status_from_config(): void
    {
        config(['docs.prerelease_versions.mobile' => [5]]);

        $this->assertTrue($this->registry->isPrerelease(DocsPlatform::Mobile, 5));
        $this->assertFalse($this->registry->isPrerelease(DocsPlatform::Mobile, 4));
    }

    #[Test]
    public function it_derives_switcher_labels_from_released_versions(): void
    {
        $this->assertSame(
            [1 => '1.x', 2 => '2.x', 3 => '3.x', 4 => '4.x'],
            $this->registry->switcherLabels(DocsPlatform::Mobile),
        );

        $this->assertSame(
            [1 => '1.x', 2 => '2.x'],
            $this->registry->switcherLabels(DocsPlatform::Desktop),
        );
    }

    #[Test]
    public function it_returns_released_minors_for_a_major(): void
    {
        $this->assertSame(
            ['4.0', '4.1', '4.2'],
            $this->registry->releasedMinors(DocsPlatform::Mobile, 4),
        );

        $this->assertSame([], $this->registry->releasedMinors(DocsPlatform::Mobile, 99));
    }

    #[Test]
    public function it_returns_renamed_pages_for_a_platform(): void
    {
        $renames = $this->registry->renames(DocsPlatform::Mobile);

        $this->assertArrayHasKey(4, $renames);
        $this->assertSame('the-basics/dialogs', $renames[4]['the-basics/dialog']);
    }
}
