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

        return view('docs.whats-new', [
            'platform' => $platform,
            'version' => $version,
            'badges' => $this->badges($platform, (int) $version),
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
