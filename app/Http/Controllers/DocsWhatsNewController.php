<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DocsPlatform;
use App\Services\DocsChangelogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class DocsWhatsNewController extends Controller
{
    public function __invoke(Request $request, string $platform, string $version): View
    {
        abort_unless(is_dir(resource_path("views/docs/{$platform}/{$version}")), 404);
        abort_unless(DocsPlatform::tryFrom($platform) !== null, 404);

        $badges = $this->badges($platform, (int) $version);

        return view('docs.whats-new', [
            'platform' => $platform,
            'version' => $version,
            'badges' => $badges,
            // Not folded into the day-long badge cache: releases move faster
            // than docs pages, and GitHub::releases() already caches hourly.
            'changelogLinks' => app(DocsChangelogService::class)->changelogLinks(
                DocsPlatform::from($platform),
                (int) $version,
                array_keys($badges),
            ),
        ]);
    }

    /**
     * @return array<string, array<string, array<int, array<string, string>>>>
     */
    private function badges(string $platform, int $version): array
    {
        $compute = fn () => app(DocsChangelogService::class)->badgesForVersion(DocsPlatform::from($platform), $version);

        if (config('app.env') === 'local') {
            return $compute();
        }

        return Cache::remember("docs_whats_new_{$platform}_{$version}", now()->addDay(), $compute);
    }
}
