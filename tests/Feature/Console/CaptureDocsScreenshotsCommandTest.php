<?php

namespace Tests\Feature\Console;

use App\Support\DocsScreenshotManifest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaptureDocsScreenshotsCommandTest extends TestCase
{
    private string $demoPath;

    private string $stagingPath;

    private string $publishPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->demoPath = sys_get_temp_dir().'/edge-components-demo-fake-'.uniqid();
        File::ensureDirectoryExists($this->demoPath.'/routes');
        File::put($this->demoPath.'/artisan', '');
        File::put($this->demoPath.'/routes/mobile.php', '');

        $this->stagingPath = sys_get_temp_dir().'/docs-screenshots-staging-'.uniqid();
        $this->publishPath = sys_get_temp_dir().'/docs-screenshots-publish-'.uniqid();

        config([
            'docs.screenshots.demo_path' => null,
            'docs.screenshots.staging_path' => $this->stagingPath,
            'docs.screenshots.publish_path' => $this->publishPath,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->demoPath);
        File::deleteDirectory($this->stagingPath);
        File::deleteDirectory($this->publishPath);

        parent::tearDown();
    }

    #[Test]
    public function it_fails_without_a_demo_path_before_running_any_process(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots')->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_when_the_path_does_not_look_like_a_demo_checkout(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => sys_get_temp_dir(),
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_for_an_invalid_platform(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'blackberry',
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_fails_for_an_unknown_only_key(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
    public function it_asks_for_the_manual_step_before_capturing_a_drawer_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'ios',
            '--only' => 'side-nav',
            '--settle-ms' => 0,
            '--full' => true,
        ])
            ->expectsQuestion(
                'Tap the ☰ button to open the side drawer for "side-nav" in the ios simulator/emulator now, then press Enter to continue',
                ''
            )
            ->assertSuccessful();
    }

    #[Test]
    public function it_skips_a_manual_step_screen_and_fails_when_run_non_interactively(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
        File::put($this->demoPath.'/artisan', "<?php\nsleep(5);\n");

        config(['docs.screenshots.process_timeout' => 1]);

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--demo-path' => $this->demoPath,
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
            '--crop-offset=0.05',
        ]);
    }

    #[Test]
    public function it_fails_for_a_crop_offset_that_leaves_no_room_for_crop_percent(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--crop-percent' => '0.5',
            '--crop-offset' => '0.5',
        ])->assertFailed();

        Process::assertNothingRan();
    }

    #[Test]
    public function it_passes_a_custom_crop_offset_to_native_screenshot(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--crop-percent' => '0.3',
            '--crop-offset' => '0.1',
        ])->assertSuccessful();

        $outputPath = $this->stagingPath.'/edge-top-bar-ios.png';

        Process::assertRan(fn ($process): bool => $process->command === [
            'php', 'artisan', 'native:screenshot', 'ios',
            '--output='.$outputPath,
            '--crop=top',
            '--crop-percent=0.3',
            '--crop-offset=0.1',
        ]);
    }

    #[Test]
    public function it_passes_the_bottom_crop_direction_for_a_bottom_nav_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
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
            '--crop-offset=0.05',
        ]);
    }

    #[Test]
    public function it_passes_no_crop_flags_for_a_side_nav_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'ios',
            '--only' => 'side-nav',
            '--settle-ms' => 0,
        ])
            ->expectsQuestion(
                'Tap the ☰ button to open the side drawer for "side-nav" in the ios simulator/emulator now, then press Enter to continue',
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
            '--demo-path' => $this->demoPath,
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

    #[Test]
    public function every_screen_is_served_from_its_own_route_in_the_demo_app(): void
    {
        foreach (DocsScreenshotManifest::keys() as $key) {
            $this->assertSame('/edge-components/'.$key, DocsScreenshotManifest::get($key)['route']);
        }
    }

    #[Test]
    public function it_falls_back_to_the_configured_demo_path(): void
    {
        Process::fake();

        config(['docs.screenshots.demo_path' => $this->demoPath]);

        $this->artisan('docs:capture-screenshots', [
            '--platform' => 'ios',
            '--only' => 'top-bar',
            '--settle-ms' => 0,
            '--full' => true,
        ])->assertSuccessful();

        Process::assertRan(fn ($process): bool => $process->path === $this->demoPath
            && ($process->command[2] ?? '') === 'native:run');
    }

    #[Test]
    public function it_asks_to_open_the_menu_before_capturing_the_menus_screen(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'android',
            '--only' => 'menus',
            '--settle-ms' => 0,
        ])
            ->expectsQuestion(
                'Tap "Export" to open its menu for "menus" in the android simulator/emulator now, then press Enter to continue',
                ''
            )
            ->assertSuccessful();
    }

    #[Test]
    public function it_captures_sheets_and_modals_without_a_manual_step(): void
    {
        Process::fake();

        $this->artisan('docs:capture-screenshots', [
            '--demo-path' => $this->demoPath,
            '--platform' => 'ios',
            '--only' => 'bottom-sheet,modal',
            '--settle-ms' => 0,
            '--no-interaction' => true,
        ])->assertSuccessful();
    }
}
