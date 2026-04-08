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
            'version' => '3',
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
}
