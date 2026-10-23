<?php

namespace Tests\Unit\Services\DocsScreenshots;

use App\Services\DocsScreenshots\ScreenshotPublisher;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScreenshotPublisherTest extends TestCase
{
    private string $stagingPath;

    private string $publishPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stagingPath = sys_get_temp_dir().'/screenshot-publisher-staging-'.uniqid();
        $this->publishPath = sys_get_temp_dir().'/screenshot-publisher-publish-'.uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->stagingPath);
        File::deleteDirectory($this->publishPath);

        parent::tearDown();
    }

    #[Test]
    public function it_copies_every_staged_file_to_the_publish_path(): void
    {
        File::ensureDirectoryExists($this->stagingPath);
        File::put($this->stagingPath.'/a.png', 'a-bytes');
        File::put($this->stagingPath.'/b.png', 'b-bytes');

        $failures = (new ScreenshotPublisher)->publish($this->stagingPath, $this->publishPath, ['a.png', 'b.png']);

        $this->assertSame([], $failures);
        $this->assertSame('a-bytes', File::get($this->publishPath.'/a.png'));
        $this->assertSame('b-bytes', File::get($this->publishPath.'/b.png'));
    }

    #[Test]
    public function it_reports_a_filename_that_was_never_staged(): void
    {
        File::ensureDirectoryExists($this->stagingPath);
        File::put($this->stagingPath.'/a.png', 'a-bytes');

        $failures = (new ScreenshotPublisher)->publish($this->stagingPath, $this->publishPath, ['a.png', 'missing.png']);

        $this->assertSame(['missing.png'], $failures);
        $this->assertFileExists($this->publishPath.'/a.png');
        $this->assertFileDoesNotExist($this->publishPath.'/missing.png');
    }

    #[Test]
    public function it_creates_the_publish_directory_when_missing(): void
    {
        File::ensureDirectoryExists($this->stagingPath);
        File::put($this->stagingPath.'/a.png', 'a-bytes');

        $this->assertDirectoryDoesNotExist($this->publishPath);

        (new ScreenshotPublisher)->publish($this->stagingPath, $this->publishPath, ['a.png']);

        $this->assertDirectoryExists($this->publishPath);
    }
}
