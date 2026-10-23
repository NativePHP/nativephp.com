<?php

declare(strict_types=1);

namespace App\Services\DocsScreenshots;

use App\Enums\DocsScreenshotCrop;
use App\Enums\DocsScreenshotPlatform;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\Process;

/**
 * Drives one screen/platform capture in an edge-components-demo checkout:
 * launches the screen with `native:run`, optionally waits for a
 * manually-confirmed step (opening a drawer or menu), then captures it with
 * `native:screenshot` — which owns
 * cropping itself (`--crop`/`--crop-percent`/`--crop-offset`), so every
 * NativePHP developer gets it too, not just this docs pipeline.
 */
final class ScreenshotCapturer
{
    private const MICROSECONDS_PER_MILLISECOND = 1_000;

    public function __construct(
        private readonly string $demoPath,
        private readonly int $timeout,
    ) {}

    /**
     * Returns null on success, or a human-readable failure reason.
     *
     * @param  ?string  $manualStep  An instruction for the user to carry out before the capture, or null.
     * @param  callable(string): void  $confirmManualStep  Prompts the user and blocks until they respond.
     */
    public function capture(
        string $key,
        DocsScreenshotPlatform $platform,
        string $route,
        ?string $manualStep,
        string $udid,
        int $settleMs,
        string $outputPath,
        DocsScreenshotCrop $crop,
        float $cropPercent,
        float $cropOffset,
        bool $isInteractive,
        callable $confirmManualStep,
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

        if ($manualStep !== null) {
            if (! $isInteractive) {
                return sprintf(
                    'Skipping %s (%s): it needs a manual step (%s), so it can only be captured interactively.',
                    $key,
                    $platform->value,
                    $manualStep
                );
            }

            $confirmManualStep(sprintf(
                '%s for "%s" in the %s simulator/emulator now, then press Enter to continue',
                $manualStep,
                $key,
                $platform->value
            ));
        }

        usleep(max(0, $settleMs) * self::MICROSECONDS_PER_MILLISECOND);

        $screenshotCommand = $this->buildArgv([
            'php', 'artisan', 'native:screenshot', $platform->value, $udid,
            '--output='.$outputPath,
            $crop === DocsScreenshotCrop::Full ? '' : '--crop='.$crop->value,
            $crop === DocsScreenshotCrop::Full ? '' : '--crop-percent='.$cropPercent,
            $crop === DocsScreenshotCrop::Full ? '' : '--crop-offset='.$cropOffset,
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
            return Process::path($this->demoPath)->timeout($this->timeout)->run($command)->successful();
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
