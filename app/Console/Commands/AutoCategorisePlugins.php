<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SuggestPluginCategories;
use App\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

final class AutoCategorisePlugins extends Command
{
    protected $signature = 'plugins:auto-categorise
        {--dry-run : Ask Jev and list its suggestions without changing any plugin\'s categories}';

    protected $description = 'Ask Jev which categories every plugin belongs in, then put each plugin in the ones it suggests';

    /**
     * Plugins Jev has already been asked about aren't asked again, so a run that
     * stops part way, or follows a dry run, picks up where the last one left off.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $plugins = Plugin::query()->orderBy('name')->get();

        if ($plugins->isEmpty()) {
            $this->info('There are no plugins to categorise.');

            return self::SUCCESS;
        }

        /** @var Collection<int, Plugin> $categorised */
        $categorised = new Collection;
        /** @var Collection<int, Plugin> $unsure */
        $unsure = new Collection;
        /** @var Collection<int, array{0: Plugin, 1: string}> $failed */
        $failed = new Collection;

        $this->withProgressBar($plugins, function (Plugin $plugin) use ($dryRun, $categorised, $unsure, $failed): void {
            if ($plugin->category_suggestions === null) {
                try {
                    (new SuggestPluginCategories($plugin))->handle();
                } catch (Throwable $exception) {
                    $failed->push([$plugin, $exception->getMessage()]);

                    return;
                }
            }

            if ($plugin->suggestedCategories()->isEmpty()) {
                $unsure->push($plugin);

                return;
            }

            if (! $dryRun) {
                $plugin->applySuggestedCategories();
            }

            $categorised->push($plugin);
        });

        $this->newLine(2);

        if ($categorised->isNotEmpty()) {
            $this->table(
                ['Plugin', $dryRun ? 'Jev Suggests' : 'Categories'],
                $categorised->map(fn (Plugin $plugin): array => [
                    $plugin->name,
                    $plugin->suggestedCategories()->map->label()->join(', '),
                ])->all()
            );
        }

        if ($unsure->isNotEmpty()) {
            $this->newLine();
            $this->warn('Jev isn\'t confident about any category for these, so they were left as they are:');
            $this->table(['Plugin'], $unsure->map(fn (Plugin $plugin): array => [$plugin->name])->all());
        }

        if ($failed->isNotEmpty()) {
            $this->newLine();
            $this->error('Jev couldn\'t be asked about these. Run the command again to retry them:');
            $this->table(
                ['Plugin', 'Error'],
                $failed->map(fn (array $row): array => [$row[0]->name, Str::limit($row[1], 100)])->all()
            );
        }

        $this->newLine();
        $this->info(sprintf('%d categorised, %d left as they are, %d failed.', $categorised->count(), $unsure->count(), $failed->count()));

        if ($dryRun) {
            $this->warn('Dry run, so no categories were changed. Run it again without --dry-run to apply these. Jev won\'t be asked again.');
        }

        return $failed->isEmpty() ? self::SUCCESS : self::FAILURE;
    }
}
