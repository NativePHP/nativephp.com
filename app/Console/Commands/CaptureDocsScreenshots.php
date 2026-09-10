<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DocsScreenshotPlatform;
use App\Support\DocsScreenshotManifest;
use Illuminate\Console\Command;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

final class CaptureDocsScreenshots extends Command
{
    protected $signature = 'docs:capture-screenshots
        {--platform=both : ios, android, or both}
        {--super-native-path= : Local checkout of NativePHP/super-native}
        {--udid= : Specific simulator/emulator UDID — required whenever native:run would otherwise have more than one device to pick from}
        {--only= : Comma-separated screen keys to limit to, e.g. top-bar,bottom-nav}
        {--settle-ms=2000 : Milliseconds to wait after launch before capturing}
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

        $stagingPath = (string) config('docs.screenshots.staging_path');
        File::ensureDirectoryExists($stagingPath);

        $timeout = (int) config('docs.screenshots.process_timeout');
        $failures = [];

        foreach ($keys as $key) {
            foreach ($platforms as $platform) {
                if (! $this->captureScreen($superNativePath, $stagingPath, $key, $platform, $timeout)) {
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

    private function captureScreen(string $superNativePath, string $stagingPath, string $key, DocsScreenshotPlatform $platform, int $timeout): bool
    {
        $screen = DocsScreenshotManifest::get($key);
        $udid = (string) $this->option('udid');

        $runCommand = $this->buildArgv([
            'php', 'artisan', 'native:run', $platform->value, $udid,
            '--build=debug',
            '--start-url='.$screen['route'],
            '--no-tty',
        ]);

        $this->info(sprintf('Launching %s on %s...', $key, $platform->value));

        if (! $this->runProcess($superNativePath, $runCommand, $timeout)) {
            $this->error(sprintf(
                'native:run failed for %s (%s). If it has more than one device to pick from, pass --udid explicitly.',
                $key,
                $platform->value
            ));

            return false;
        }

        if ($screen['requires_drawer_open']) {
            if (! $this->input->isInteractive()) {
                $this->error(sprintf(
                    'Skipping %s (%s): opening its drawer needs a manual step, so it can only be captured interactively.',
                    $key,
                    $platform->value
                ));

                return false;
            }

            $this->ask(sprintf(
                'Manually open the side drawer for "%s" in the %s simulator/emulator now, then press Enter to continue',
                $key,
                $platform->value
            ));
        }

        usleep(max(0, (int) $this->option('settle-ms')) * 1000);

        $outputPath = sprintf('%s/%s', $stagingPath, $screen[$platform->value]);

        $screenshotCommand = $this->buildArgv([
            'php', 'artisan', 'native:screenshot', $platform->value, $udid,
            '--output='.$outputPath,
        ]);

        if (! $this->runProcess($superNativePath, $screenshotCommand, $timeout)) {
            $this->error(sprintf('native:screenshot failed for %s (%s).', $key, $platform->value));

            return false;
        }

        $this->info(sprintf('Captured %s.', $outputPath));

        return true;
    }

    /**
     * Runs an artisan command in the super-native checkout. `native:run`
     * prompts interactively when `--udid` is ambiguous and no real terminal
     * is attached to this subprocess, which can time out rather than fail
     * fast — caught here and reported as an ordinary failure.
     *
     * @param  list<string>  $command
     */
    private function runProcess(string $superNativePath, array $command, int $timeout): bool
    {
        try {
            return Process::path($superNativePath)->timeout($timeout)->run($command)->successful();
        } catch (ProcessTimedOutException) {
            return false;
        }
    }

    /**
     * Drop empty arguments (an omitted `--udid`) so the target command sees
     * a clean argv instead of a blank positional argument.
     *
     * @param  list<string>  $argv
     * @return list<string>
     */
    private function buildArgv(array $argv): array
    {
        return array_values(array_filter($argv, fn (string $argument): bool => $argument !== ''));
    }

    /**
     * @param  list<string>  $keys
     * @param  list<DocsScreenshotPlatform>  $platforms
     */
    private function publish(string $stagingPath, array $keys, array $platforms): bool
    {
        $publishPath = (string) config('docs.screenshots.publish_path');
        File::ensureDirectoryExists($publishPath);

        $failures = [];

        foreach ($keys as $key) {
            foreach ($platforms as $platform) {
                $filename = DocsScreenshotManifest::get($key)[$platform->value];
                $source = sprintf('%s/%s', $stagingPath, $filename);

                if (! File::exists($source) || ! File::copy($source, sprintf('%s/%s', $publishPath, $filename))) {
                    $failures[] = $filename;
                }
            }
        }

        if ($failures !== []) {
            $this->error(sprintf('Failed to publish: %s', implode(', ', $failures)));

            return false;
        }

        $this->info(sprintf('Published %s.', $publishPath));

        return true;
    }
}
