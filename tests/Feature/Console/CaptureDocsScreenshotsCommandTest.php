<?php

namespace Tests\Feature\Console;

use App\Support\DocsScreenshotManifest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaptureDocsScreenshotsCommandTest extends TestCase
{
    private string $superNativePath;

    private string $stagingPath;

    private string $publishPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superNativePath = sys_get_temp_dir().'/super-native-fake-'.uniqid();
        File::ensureDirectoryExists($this->superNativePath.'/routes');
        File::put($this->superNativePath.'/artisan', '');
        File::put($this->superNativePath.'/routes/mobile.php', '');

        $this->stagingPath = sys_get_temp_dir().'/docs-screenshots-staging-'.uniqid();
        $this->publishPath = sys_get_temp_dir().'/docs-screenshots-publish-'.uniqid();

        config([
            'docs.screenshots.staging_path' => $this->stagingPath,
            'docs.screenshots.publish_path' => $this->publishPath,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->superNativePath);
        File::deleteDirectory($this->stagingPath);
        File::deleteDirectory($this->publishPath);

        parent::tearDown();
    }

    #[Test]
    public function it_fails_without_a_super_native_path_before_running_any_process(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots')->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_when_the_path_does_not_look_like_a_super_native_checkout(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => sys_get_temp_dir(),
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_for_an_invalid_platform(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'blackberry',
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_for_an_unknown_only_key(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--only' => 'not-a-real-screen',
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function the_manifest_has_both_filenames_for_every_screen_with_no_collisions(): void
    {
        $seen = [];

        foreach (DocsScreenshotManifest::keys() as $key) {
            $screen = DocsScreenshotManifest::get($key);

            $this->assertNotSame('', $screen['ios']);
            $this->assertNotSame('', $screen['android']);

            foreach ([$screen['ios'], $screen['android']] as $filename) {
                $this->assertArrayNotHasKey($filename, $seen, "Filename [{$filename}] is used by more than one screen.");
                $seen[$filename] = $key;
            }
        }
    }

    #[Test]
    public function it_captures_a_single_screen_to_staging_without_publishing(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--full' => true,
        ])->assertSuccessful();

        $outputPath = $this->stagingPath.'/edge-top-bar-ios.png';

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:run', 'ios', '--build=debug', '--start-url=/edge-components/top-bar', '--no-tty',
        ]);

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:screenshot', 'ios', '--output='.$outputPath,
        ]);

        $this->assertFileDoesNotExist($this->publishPath.'/edge-top-bar-ios.png');
    }

    #[Test]
    public function it_passes_the_udid_through_to_both_commands(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'android',
            '--only' => 'bottom-nav',
            '--udid' => 'emulator-5554',
            '--settle-ms' => 0,
            '--full' => true,
        ])->assertSuccessful();

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:run', 'android', 'emulator-5554', '--build=debug', '--start-url=/edge-components/bottom-nav', '--no-tty',
        ]);
    }

    #[Test]
    public function it_fails_the_whole_run_when_one_capture_fails(): void
    {
        Process::fake([
            '*native:run*' => Process::result(exitCode: 1),
        ]);

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
        ])->assertFailed();
    }

    #[Test]
    public function it_publishes_staged_screenshots_when_requested(): void
    {
        Process::fake();

        File::ensureDirectoryExists($this->stagingPath);
        File::put($this->stagingPath.'/edge-top-bar-ios.png', 'fake-png-bytes');

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--full' => true,
            '--publish' => true,
        ])->assertSuccessful();

        $this->assertFileExists($this->stagingPath.'/edge-top-bar-ios.png');
        $this->assertFileExists($this->publishPath.'/edge-top-bar-ios.png');
        $this->assertSame('fake-png-bytes', File::get($this->publishPath.'/edge-top-bar-ios.png'));
    }

    #[Test]
    public function it_asks_to_manually_open_the_drawer_for_a_drawer_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'side-nav',
            '--settle-ms' => 0,
            '--full' => true,
        ])
            ->expectsQuestion(
                'Manually open the side drawer for "side-nav" in the ios simulator/emulator now, then press Enter to continue',
                ''
            )
            ->assertSuccessful();
    }

    #[Test]
    public function it_skips_a_drawer_screen_and_fails_when_run_non_interactively(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'side-nav',
            '--settle-ms' => 0,
            '--no-interaction' => true,
        ])->assertFailed();

        Process::assertNotRan(fn ($process): bool => ($process->command[2] ?? '') === 'native:screenshot');
    }

    #[Test]
    public function it_clamps_a_negative_settle_ms_instead_of_crashing(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => -500,
            '--full' => true,
        ])->assertSuccessful();
    }

    #[Test]
    public function it_fails_to_publish_a_screen_never_staged(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--full' => true,
            '--publish' => true,
        ])->assertFailed();
    }

    #[Test]
    public function it_fails_cleanly_instead_of_crashing_when_a_capture_step_times_out(): void
    {
        // No Process::fake() here — this exercises a real subprocess that
        // outlives the configured timeout, to prove the timeout is caught
        // rather than left to crash the command as an uncaught exception.
        File::put($this->superNativePath.'/artisan', "<?php\nsleep(5);\n");

        config(['docs.screenshots.process_timeout' => 1]);

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
        ])->assertFailed();
    }

    #[Test]
    public function it_dry_runs_without_running_any_process(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar,side-nav',
            '--dry-run' => true,
        ])->assertSuccessful();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_for_an_invalid_crop_percent(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--crop-percent' => '1.5',
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_passes_the_screen_crop_direction_and_percent_to_native_screenshot(): void
    {
        // Cropping itself is native:screenshot's job (mobile-air) — this
        // only verifies the right flags reach it for a top-bar screen.
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--crop-percent' => '0.3',
        ])->assertSuccessful();

        $outputPath = $this->stagingPath.'/edge-top-bar-ios.png';

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:screenshot', 'ios',
            '--output='.$outputPath,
            '--crop=top',
            '--crop-percent=0.3',
        ]);
    }

    #[Test]
    public function it_passes_the_bottom_crop_direction_for_a_bottom_nav_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'android',
            '--only' => 'bottom-nav',
            '--settle-ms' => 0,
        ])->assertSuccessful();

        $outputPath = $this->stagingPath.'/edge-bottom-nav-android.png';

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:screenshot', 'android',
            '--output='.$outputPath,
            '--crop=bottom',
            '--crop-percent=0.15',
        ]);
    }

    #[Test]
    public function it_passes_no_crop_flags_for_a_side_nav_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'side-nav',
            '--settle-ms' => 0,
        ])
            ->expectsQuestion(
                'Manually open the side drawer for "side-nav" in the ios simulator/emulator now, then press Enter to continue',
                ''
            )
            ->assertSuccessful();

        $outputPath = $this->stagingPath.'/edge-side-nav-ios.png';

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:screenshot', 'ios',
            '--output='.$outputPath,
        ]);
    }

    #[Test]
    public function it_passes_no_crop_flags_when_full_is_requested(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--super-native-path' => $this->superNativePath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--full' => true,
        ])->assertSuccessful();

        $outputPath = $this->stagingPath.'/edge-top-bar-ios.png';

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:screenshot', 'ios',
            '--output='.$outputPath,
        ]);
    }
}
