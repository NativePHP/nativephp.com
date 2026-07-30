<?php

namespace Tests\Unit;

use App\Services\DocsVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsVersionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DocsVersionService $docsVersionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docsVersionService = app(DocsVersionService::class);
    }

    public static function platformDataProvider(): array
    {
        return [
            'platform=mobile' => ['mobile'],
            'platform=desktop' => ['desktop'],
        ];
    }

    #[Test]
    #[DataProvider('platformDataProvider')]
    public function it_points_to_the_latest_version_when_page_exists_in_latest(
        string $platform,
    ): void {
        $latestVersion = $platform === 'mobile' ? config('docs.latest_versions.mobile') : config('docs.latest_versions.desktop');

        $url = $this->docsVersionService->determineCanonicalUrl(
            platform: $platform,
            page: 'getting-started/introduction',
        );

        $expected = route('docs.show', [
            'platform' => $platform,
            'version' => $latestVersion,
            'page' => 'getting-started/introduction',
        ]);

        $this->assertEquals($expected, $url);
    }

    #[Test]
    public function it_remaps_mobile_apis_pages_to_plugins_core_when_the_page_exists_there_in_latest(): void
    {
        $url = $this->docsVersionService->determineCanonicalUrl(
            platform: 'mobile',
            page: 'apis/camera',
        );

        $expected = route('docs.show', [
            'platform' => 'mobile',
            'version' => config('docs.latest_versions.mobile'),
            'page' => 'plugins/core/camera',
        ]);

        $this->assertEquals($expected, $url);
    }

    #[Test]
    public function it_points_to_the_latest_version_of_getting_started_introduction_when_page_does_not_exist_in_latest(): void
    {
        // concepts/ci-cd only exists in version 1 of mobile documentation
        $url = $this->docsVersionService->determineCanonicalUrl(
            platform: 'mobile',
            page: 'concepts/ci-cd',
        );

        $expected = route('docs.show', [
            'platform' => 'mobile',
            'version' => config('docs.latest_versions.mobile'),
            'page' => 'getting-started/introduction',
        ]);

        $this->assertEquals($expected, $url);
    }

    #[Test]
    #[DataProvider('platformDataProvider')]
    public function it_points_to_the_latest_version_of_getting_started_introduction_when_page_does_not_exist(
        string $platform,
    ): void {
        $latestVersion = $platform === 'mobile' ? config('docs.latest_versions.mobile') : config('docs.latest_versions.desktop');

        $url = $this->docsVersionService->determineCanonicalUrl(
            platform: $platform,
            page: 'non-existent-page',
        );

        $expected = route('docs.show', [
            'platform' => $platform,
            'version' => $latestVersion,
            'page' => 'getting-started/introduction',
        ]);

        $this->assertEquals($expected, $url);
    }

    #[Test]
    public function it_maps_renamed_concepts_pages_to_digging_deeper_in_v4_and_back_in_older_versions(): void
    {
        // Old slug resolves forward on v4 (the version the rename happened in).
        $this->assertSame(
            'digging-deeper/security',
            $this->docsVersionService->resolvePageForVersion('mobile', 4, 'concepts/security'),
        );

        // New slug resolves back to the old slug when targeting an older version.
        $this->assertSame(
            'concepts/security',
            $this->docsVersionService->resolvePageForVersion('mobile', 3, 'digging-deeper/security'),
        );
    }

    #[Test]
    public function it_maps_deployment_to_the_publishing_section_across_versions(): void
    {
        // Older versions keep deployment under getting-started; on v4 it forwards to Publishing.
        $this->assertSame(
            'publishing/introduction',
            $this->docsVersionService->resolvePageForVersion('mobile', 4, 'getting-started/deployment'),
        );

        // Switching the new Publishing intro back to an older version lands on deployment.
        $this->assertSame(
            'getting-started/deployment',
            $this->docsVersionService->resolvePageForVersion('mobile', 3, 'publishing/introduction'),
        );
    }

    #[Test]
    public function it_flattens_super_native_pages_into_the_digging_deeper_section_in_v4(): void
    {
        // The old SuperNative introduction becomes the SuperNative page in Architecture.
        $this->assertSame(
            'architecture/super-native',
            $this->docsVersionService->resolvePageForVersion('mobile', 4, 'super-native/introduction'),
        );

        // Some former SuperNative pages moved into The Basics.
        $this->assertSame(
            'the-basics/routing',
            $this->docsVersionService->resolvePageForVersion('mobile', 4, 'super-native/navigation'),
        );
        $this->assertSame(
            'the-basics/layouts',
            $this->docsVersionService->resolvePageForVersion('mobile', 4, 'super-native/layouts'),
        );

        // The rest became direct Digging Deeper pages.
        $this->assertSame(
            'digging-deeper/data-binding',
            $this->docsVersionService->resolvePageForVersion('mobile', 4, 'super-native/data-binding'),
        );

        // Resolves back to the old paths when targeting an older version.
        $this->assertSame(
            'super-native/introduction',
            $this->docsVersionService->resolvePageForVersion('mobile', 3, 'architecture/super-native'),
        );
        $this->assertSame(
            'super-native/layouts',
            $this->docsVersionService->resolvePageForVersion('mobile', 3, 'the-basics/layouts'),
        );
    }
}
