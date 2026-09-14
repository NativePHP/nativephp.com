<?php

declare(strict_types=1);

namespace App\Services\DocsScreenshots;

use Illuminate\Support\Facades\File;

/**
 * Copies staged screenshots into their public docs path.
 */
final class ScreenshotPublisher
{
    /**
     * Copies each filename from $stagingPath to $publishPath.
     *
     * @param  list<string>  $filenames
     * @return list<string> the filenames that failed to publish (empty on full success)
     */
    public function publish(string $stagingPath, string $publishPath, array $filenames): array
    {
        File::ensureDirectoryExists($publishPath);

        $failures = [];

        foreach ($filenames as $filename) {
            $source = sprintf('%s/%s', $stagingPath, $filename);
            $destination = sprintf('%s/%s', $publishPath, $filename);

            if (! File::exists($source) || ! File::copy($source, $destination)) {
                $failures[] = $filename;
            }
        }

        return $failures;
    }
}
