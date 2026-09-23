<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\PluginCategory;
use App\Models\Plugin;
use App\Services\PluginCategoryClassifier;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class BackfillPluginCategories extends Command
{
    protected $signature = 'plugins:backfill-categories
        {--dry-run : Preview proposed categories without saving them}';

    protected $description = 'Assign a category to already-published plugins that have none, using a keyword-based heuristic';

    public function handle(PluginCategoryClassifier $classifier): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $plugins = Plugin::query()->whereNull('category')->get();

        if ($plugins->isEmpty()) {
            $this->info('No plugins need a category backfilled.');

            return self::SUCCESS;
        }

        /** @var Collection<int, array{0: Plugin, 1: PluginCategory}> $matched */
        $matched = new Collection;
        /** @var Collection<int, Plugin> $unmatched */
        $unmatched = new Collection;

        foreach ($plugins as $plugin) {
            $category = $classifier->classify($plugin);

            if ($category === null) {
                $unmatched->push($plugin);

                continue;
            }

            $matched->push([$plugin, $category]);

            if (! $dryRun) {
                $plugin->update(['category' => $category]);
            }
        }

        if ($matched->isNotEmpty()) {
            $this->table(
                ['Plugin', 'Proposed Category'],
                $matched->map(fn (array $row): array => [$row[0]->name, $row[1]->label()])->all()
            );
        }

        if ($unmatched->isNotEmpty()) {
            $this->newLine();
            $this->warn('No confident match — left uncategorized for a manual look:');
            $this->table(['Plugin'], $unmatched->map(fn (Plugin $plugin): array => [$plugin->name])->all());
        }

        $this->newLine();
        $this->info(sprintf('%d matched, %d left uncategorized.', $matched->count(), $unmatched->count()));

        if ($dryRun) {
            $this->warn('Dry run — no changes were saved. Run again without --dry-run to apply.');
        }

        return self::SUCCESS;
    }
}
