<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DocsScreenshotCrop;
use App\Enums\DocsScreenshotPlatform;
use App\Services\DocsScreenshots\ScreenshotCapturer;
use App\Services\DocsScreenshots\ScreenshotPublisher;
use App\Support\DocsScreenshotManifest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class CaptureDocsScreenshots extends Command
{
    protected $signature = 'docs:capture-screenshots
        {--platform=both : ios, android, or both}
        {--super-native-path= : Local checkout of NativePHP/super-native}
        {--udid= : Specific simulator/emulator UDID — required whenever native:run would otherwise have more than one device to pick from}
        {--only= : Comma-separated screen keys to limit to, e.g. top-bar,bottom-nav}
        {--settle-ms=2000 : Milliseconds to wait after launch before capturing}
        {--full : Keep the full, uncropped screenshot instead of the tight top/bottom crop most screens use}
        {--crop-percent= : Override the configured crop_percent for this run (0-1)}
        {--dry-run : Print what would be captured without running anything}
        {--publish : Copy every staged screenshot into public/img/docs}';

    protected $description = "Regenerate the mobile docs' Edge Component screenshots from a running super-native checkout";

    public function handle(): int
    {
        $superNativePath = $this->resolveSuperNativePath();

        if ($superNativePath === null) {
            return self::FAILURE;
        }

        $platforms = DocsScreenshotPlatform::fromOption((string) $this->option('platform'));

        if ($platforms === []) {
            $this->error(sprintf("Invalid --platform '%s'. Use 'ios', 'android', or 'both'.", $this->option('platform')));

            return self::FAILURE;
        }

        $keys = $this->resolveScreenKeys();

        if ($keys === null) {
            return self::FAILURE;
        }

        $cropPercent = $this->resolveCropPercent();

        if ($cropPercent === null) {
            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->printDryRun($superNativePath, $keys, $platforms, $cropPercent);

            return self::SUCCESS;
        }

        $stagingPath = (string) config('docs.screenshots.staging_path');
        File::ensureDirectoryExists($stagingPath);

        $capturer = new ScreenshotCapturer($superNativePath, (int) config('docs.screenshots.process_timeout'));
        $failures = [];

        foreach ($keys as $key) {
            foreach ($platforms as $platform) {
                if (! $this->captureScreen($capturer, $stagingPath, $key, $platform, $cropPercent)) {
                    $failures[] = sprintf('%s (%s)', $key, $platform->value);
                }
            }
        }

        if ($failures !== []) {
            $this->error(sprintf('Failed to capture: %s', implode(', ', $failures)));

            return self::FAILURE;
        }

        $this->info(sprintf('Captured %d screen(s) into %s', count($keys), $stagingPath));

        if ($this->option('publish') && ! $this->publish($stagingPath, $keys, $platforms)) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function resolveSuperNativePath(): ?string
    {
        $path = rtrim((string) $this->option('super-native-path'), '/');

        if ($path === '') {
            $this->error('--super-native-path is required — pass the local checkout of NativePHP/super-native, e.g. a sibling directory of this repo.');

            return null;
        }

        if (! is_dir($path) || ! is_file($path.'/artisan') || ! is_file($path.'/routes/mobile.php')) {
            $this->error(sprintf("'%s' doesn't look like a super-native checkout (missing artisan or routes/mobile.php).", $path));

            return null;
        }

        return $path;
    }

    /**
     * @return list<string>|null
     */
    private function resolveScreenKeys(): ?array
    {
        $only = (string) $this->option('only');

        if ($only === '') {
            return DocsScreenshotManifest::keys();
        }

        $keys = array_filter(array_map('trim', explode(',', $only)));
        $unknown = array_filter($keys, fn (string $key): bool => ! DocsScreenshotManifest::has($key));

        if ($unknown !== []) {
            $this->error(sprintf('Unknown screen key(s): %s', implode(', ', $unknown)));

            return null;
        }

        return array_values($keys);
    }

    private function resolveCropPercent(): ?float
    {
        $given = (string) $this->option('crop-percent');
        $percent = $given === '' ? (float) config('docs.screenshots.crop_percent') : (float) $given;

        if ($percent <= 0 || $percent >= 1) {
            $this->error(sprintf('--crop-percent must be between 0 and 1 (exclusive), got %s.', $given));

            return null;
        }

        return $percent;
    }

    /**
     * @param  list<string>  $keys
     * @param  list<DocsScreenshotPlatform>  $platforms
     */
    private function printDryRun(string $superNativePath, array $keys, array $platforms, float $cropPercent): void
    {
        $this->info(sprintf('Would use super-native checkout: %s', $superNativePath));

        foreach ($keys as $key) {
            $screen = DocsScreenshotManifest::get($key);

            foreach ($platforms as $platform) {
                $crop = $this->option('full') || $screen['crop'] === DocsScreenshotCrop::Full
                    ? 'full'
                    : sprintf('%s %d%%', $screen['crop']->value, (int) round($cropPercent * 100));

                $this->line(sprintf(
                    '  %s (%s) — route %s — %s%s',
                    $key,
                    $platform->value,
                    $screen['route'],
                    $crop,
                    $screen['requires_drawer_open'] ? ' — needs a manual drawer-open step' : ''
                ));
            }
        }
    }

    private function captureScreen(
        ScreenshotCapturer $capturer,
        string $stagingPath,
        string $key,
        DocsScreenshotPlatform $platform,
        float $cropPercent,
    ): bool {
        $screen = DocsScreenshotManifest::get($key);
        $outputPath = sprintf('%s/%s', $stagingPath, $screen[$platform->value]);
        $crop = $this->option('full') ? DocsScreenshotCrop::Full : $screen['crop'];

        $failure = $capturer->capture(
            key: $key,
            platform: $platform,
            route: $screen['route'],
            requiresDrawerOpen: $screen['requires_drawer_open'],
            udid: (string) $this->option('udid'),
            settleMs: (int) $this->option('settle-ms'),
            outputPath: $outputPath,
            crop: $crop,
            cropPercent: $cropPercent,
            isInteractive: $this->input->isInteractive(),
            confirmDrawerOpen: fn (string $message) => $this->ask($message),
        );

        if ($failure !== null) {
            $this->error($failure);

            return false;
        }

        $this->info($crop === DocsScreenshotCrop::Full
            ? sprintf('Captured %s (full, uncropped).', $outputPath)
            : sprintf('Captured %s (cropped to %s).', $outputPath, $crop->value));

        return true;
    }

    /**
     * @param  list<string>  $keys
     * @param  list<DocsScreenshotPlatform>  $platforms
     */
    private function publish(string $stagingPath, array $keys, array $platforms): bool
    {
        $publishPath = (string) config('docs.screenshots.publish_path');

        $filenames = [];

        foreach ($keys as $key) {
            foreach ($platforms as $platform) {
                $filenames[] = DocsScreenshotManifest::get($key)[$platform->value];
            }
        }

        $failures = (new ScreenshotPublisher)->publish($stagingPath, $publishPath, $filenames);

        if ($failures !== []) {
            $this->error(sprintf('Failed to publish: %s', implode(', ', $failures)));

            return false;
        }

        $this->info(sprintf('Published %s.', $publishPath));

        return true;
    }
}
