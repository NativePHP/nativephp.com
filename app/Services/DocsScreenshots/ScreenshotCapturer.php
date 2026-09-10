<?php

declare(strict_types=1);

namespace App\Services\DocsScreenshots;

use App\Enums\DocsScreenshotCrop;
use App\Enums\DocsScreenshotPlatform;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\Process;

/**
 * Drives one screen/platform capture in a super-native checkout: launches
 * the screen with `native:run`, optionally waits for a manually-confirmed
 * drawer-open step, then captures it with `native:screenshot` — which owns
 * cropping itself (`--crop`/`--crop-percent`), so every NativePHP developer
 * gets it too, not just this docs pipeline.
 */
final class ScreenshotCapturer
{
    public function __construct(
        private readonly string $superNativePath,
        private readonly int $timeout,
    ) {}

    /**
     * Returns null on success, or a human-readable failure reason.
     *
     * @param  callable(string): void  $confirmDrawerOpen  Prompts the user and blocks until they respond.
     */
    public function capture(
        string $key,
        DocsScreenshotPlatform $platform,
        string $route,
        bool $requiresDrawerOpen,
        string $udid,
        int $settleMs,
        string $outputPath,
        DocsScreenshotCrop $crop,
        float $cropPercent,
        bool $isInteractive,
        callable $confirmDrawerOpen,
    ): ?string {
        $runCommand = $this->buildArgv([
            'php', 'artisan', 'native:run', $platform->value, $udid,
            '--build=debug',
            '--start-url='.$route,
            '--no-tty',
        ]);

        if (! $this->runProcess($runCommand)) {
            return sprintf(
                'native:run failed for %s (%s). If it has more than one device to pick from, pass --udid explicitly.',
                $key,
                $platform->value
            );
        }

        if ($requiresDrawerOpen) {
            if (! $isInteractive) {
                return sprintf(
                    'Skipping %s (%s): opening its drawer needs a manual step, so it can only be captured interactively.',
                    $key,
                    $platform->value
                );
            }

            $confirmDrawerOpen(sprintf(
                'Manually open the side drawer for "%s" in the %s simulator/emulator now, then press Enter to continue',
                $key,
                $platform->value
            ));
        }

        usleep(max(0, $settleMs) * 1000);

        $screenshotCommand = $this->buildArgv([
            'php', 'artisan', 'native:screenshot', $platform->value, $udid,
            '--output='.$outputPath,
            $crop === DocsScreenshotCrop::Full ? '' : '--crop='.$crop->value,
            $crop === DocsScreenshotCrop::Full ? '' : '--crop-percent='.$cropPercent,
        ]);

        if (! $this->runProcess($screenshotCommand)) {
            return sprintf('native:screenshot failed for %s (%s).', $key, $platform->value);
        }

        return null;
    }

    /**
     * `native:run` prompts interactively when `--udid` is ambiguous and no
     * real terminal is attached to this subprocess, which can time out
     * rather than fail fast — caught here and reported as an ordinary
     * failure instead of crashing the caller.
     *
     * @param  list<string>  $command
     */
    private function runProcess(array $command): bool
    {
        try {
            return Process::path($this->superNativePath)->timeout($this->timeout)->run($command)->successful();
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
}
