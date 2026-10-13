<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DocsPlatform;
use App\Models\DocsCoverageGap;
use App\Services\DocsCoverageAuditor;
use Illuminate\Console\Command;

final class AuditDocsCoverage extends Command
{
    protected $signature = 'docs:audit-coverage {platform? : desktop or mobile — every platform if omitted}';

    protected $description = "Cross-reference each platform's config keys and console commands against the docs, and record what's undocumented";

    public function handle(DocsCoverageAuditor $auditor): int
    {
        if ($this->argument('platform')) {
            $platform = DocsPlatform::tryFrom($this->argument('platform'));

            if ($platform === null) {
                $this->error("Unknown platform: {$this->argument('platform')}");

                return self::FAILURE;
            }

            $platforms = [$platform];
        } else {
            $platforms = DocsPlatform::cases();
        }

        foreach ($platforms as $platform) {
            $this->auditPlatform($auditor, $platform);
        }

        return self::SUCCESS;
    }

    private function auditPlatform(DocsCoverageAuditor $auditor, DocsPlatform $platform): void
    {
        $results = $auditor->audit($platform);

        if ($results->isEmpty()) {
            $this->error("{$platform->label()}: audit returned nothing (a fetch likely failed) — leaving stored gaps untouched.");

            return;
        }

        $checkedAt = now();

        foreach ($results as $result) {
            DocsCoverageGap::query()->updateOrCreate(
                [
                    'platform' => $platform,
                    'category' => $result['category'],
                    'identifier' => $result['identifier'],
                ],
                [
                    'documented' => $result['documented'],
                    'source_path' => $result['source_path'],
                    'checked_at' => $checkedAt,
                ]
            );
        }

        // Stale identifiers (renamed, removed) are dropped per category —
        // scoped this way so a category the auditor didn't touch this run
        // (e.g. no command files matched) can't have its untouched rows
        // wiped by another category's identifier list.
        foreach ($results->groupBy(fn (array $result) => $result['category']->value) as $category => $categoryResults) {
            DocsCoverageGap::query()
                ->where('platform', $platform)
                ->where('category', $category)
                ->whereNotIn('identifier', $categoryResults->pluck('identifier'))
                ->delete();
        }

        $undocumented = $results->reject(fn (array $result) => $result['documented']);

        $this->info(sprintf(
            '%s: %d checked, %d undocumented',
            $platform->label(),
            $results->count(),
            $undocumented->count()
        ));

        foreach ($undocumented as $gap) {
            $this->line(sprintf('  - [%s] %s', $gap['category']->label(), $gap['identifier']));
        }
    }
}
