<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocsCoverageCategory;
use App\Enums\DocsPlatform;
use App\Support\GitHub;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class DocsCoverageAuditor
{
    public function __construct(
        private DocsVersionRegistry $registry
    ) {}

    /**
     * @return Collection<int, array{category: DocsCoverageCategory, identifier: string, documented: bool, source_path: string}>
     */
    public function audit(DocsPlatform $platform): Collection
    {
        $repo = $this->registry->sourceRepo($platform);
        $github = new GitHub($repo['package']);
        $tree = $github->tree();
        $docsText = $this->docsText($platform);

        return $this->auditConfigKeys($github, $tree, $docsText, $repo['config_path'])
            ->concat($this->auditConsoleCommands($github, $tree, $docsText))
            ->values();
    }

    /**
     * @param  Collection<int, array{path: string, type: string, sha: string}>  $tree
     * @return Collection<int, array{category: DocsCoverageCategory, identifier: string, documented: bool, source_path: string}>
     */
    private function auditConfigKeys(GitHub $github, Collection $tree, string $docsText, string $configPath): Collection
    {
        $entry = $tree->firstWhere('path', $configPath);

        if ($entry === null) {
            return collect();
        }

        $contents = $github->blob($entry['sha']);

        if ($contents === null) {
            return collect();
        }

        // Matches keys indented 4 (top-level) or 8 spaces (one level nested,
        // e.g. inside 'android' => [...]) — config/nativephp.php groups most
        // platform-specific options that way. Deeper nesting is skipped to
        // avoid false positives from array values that happen to look like
        // 'key' => in unrelated data (e.g. permission description strings).
        preg_match_all("/^ {4,8}'([a-z_]+)'\s*=>/m", $contents, $matches);

        return collect(array_unique($matches[1]))
            ->map(fn (string $key): array => [
                'category' => DocsCoverageCategory::ConfigKey,
                'identifier' => $key,
                'documented' => (bool) preg_match('/\b'.preg_quote($key, '/').'\b/', $docsText),
                'source_path' => $configPath,
            ]);
    }

    /**
     * @param  Collection<int, array{path: string, type: string, sha: string}>  $tree
     * @return Collection<int, array{category: DocsCoverageCategory, identifier: string, documented: bool, source_path: string}>
     */
    private function auditConsoleCommands(GitHub $github, Collection $tree, string $docsText): Collection
    {
        return $tree
            ->filter(fn (array $entry): bool => $entry['type'] === 'blob' && (bool) preg_match('#/Commands/[^/]+\.php$#', $entry['path']))
            ->map(function (array $entry) use ($github): ?array {
                $contents = $github->blob($entry['sha']);

                if ($contents === null || ! preg_match('/protected \$signature\s*=\s*[\'"]([a-z0-9:_-]+)/i', $contents, $match)) {
                    return null;
                }

                return ['identifier' => $match[1], 'source_path' => $entry['path']];
            })
            ->filter()
            ->unique('identifier')
            ->map(fn (array $command): array => [
                'category' => DocsCoverageCategory::ConsoleCommand,
                'identifier' => $command['identifier'],
                'documented' => (bool) preg_match('/\b'.preg_quote($command['identifier'], '/').'\b/', $docsText),
                'source_path' => $command['source_path'],
            ])
            ->values();
    }

    protected function docsText(DocsPlatform $platform): string
    {
        $directory = resource_path('views/docs/'.$platform->value);

        if (! is_dir($directory)) {
            return '';
        }

        $finder = (new Finder)->files()->in($directory)->name('*.md');

        $text = '';

        foreach ($finder as $file) {
            $text .= $file->getContents()."\n";
        }

        return $text;
    }
}
