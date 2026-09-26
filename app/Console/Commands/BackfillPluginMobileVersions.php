<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class BackfillPluginMobileVersions extends Command
{
    protected $signature = 'plugins:backfill-mobile-versions
        {--dry-run : List the versions each plugin would get without saving them}';

    protected $description = 'Work out which NativePHP Mobile versions each plugin supports from the composer.json saved at its last sync';

    /**
     * Nothing is fetched from GitHub, so it's quick and safe to run again, e.g. after
     * adding a major version to config('plugins.mobile_major_versions').
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        /** @var array<int, array{0: string, 1: string, 2: string}> $changed */
        $changed = [];
        /** @var array<int, array{0: string, 1: string}> $unmatched */
        $unmatched = [];

        foreach (Plugin::query()->orderBy('name')->get() as $plugin) {
            $constraint = data_get($plugin->composer_data, 'require.nativephp/mobile');
            $versions = Plugin::mobileVersionsFromComposer($plugin->composer_data);

            if (is_string($constraint) && $versions === null) {
                $unmatched[] = [$plugin->name, $constraint];
            }

            if (($versions ?? []) === $plugin->supportedMobileVersions()) {
                continue;
            }

            $changed[] = [$plugin->name, is_string($constraint) ? $constraint : '—', $versions ? implode(', ', $versions) : '—'];

            if (! $dryRun) {
                $plugin->update(['mobile_versions' => $versions]);
            }
        }

        if ($changed !== []) {
            $this->table(['Plugin', 'Requires', 'Versions'], $changed);
        }

        if ($unmatched !== []) {
            $majorVersions = collect(config('plugins.mobile_major_versions', []))->map(fn (int $major): string => "{$major}.x");

            $this->newLine();
            $this->warn("These require nativephp/mobile but don't allow any {$majorVersions->join(', ', ' or ')} release, so they have no versions:");
            $this->table(['Plugin', 'Requires'], $unmatched);
        }

        $this->newLine();
        $this->info(sprintf('%d %s %s.', count($changed), Str::plural('plugin', count($changed)), $dryRun ? 'would change' : 'updated'));

        if ($dryRun) {
            $this->warn('Dry run, so nothing was saved. Run it again without --dry-run to save these.');
        }

        return self::SUCCESS;
    }
}
