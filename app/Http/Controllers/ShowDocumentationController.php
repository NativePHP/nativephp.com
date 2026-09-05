<?php

namespace App\Http\Controllers;

use App\Enums\DocsPlatform;
use App\Services\DocsNavigationService;
use App\Services\DocsVersionRegistry;
use App\Services\DocsVersionService;
use App\Support\CommonMark\CommonMark;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Closure;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;
use Spatie\Menu\Html;
use Spatie\Menu\Menu;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ShowDocumentationController extends Controller
{
    public function __invoke(Request $request, string $platform, string $version, ?string $page = null)
    {
        abort_unless(is_dir(resource_path('views/docs/'.$platform.'/'.$version)), 404);
        // The route only constrains {platform} to [a-z]+, so a real docs
        // directory isn't proof it's one DocsPlatform::from() below can resolve.
        abort_unless(DocsPlatform::tryFrom($platform) !== null, 404);

        session(['viewing_docs_version' => $version]);
        session(['viewing_docs_platform' => $platform]);

        $fingerprint = $this->fingerprint($platform, $version);

        $navigation = $this->cacheOrCompute("docs_nav_{$platform}_{$version}", $fingerprint,
            fn () => app(DocsNavigationService::class)->build(DocsPlatform::from($platform), (int) $version)
        );

        if (is_null($page)) {
            return $this->redirectToFirstNavigationPage($navigation);
        }

        try {
            $pageProperties = $this->cacheOrCompute("docs_{$platform}_{$version}_{$page}", $fingerprint,
                fn () => $this->getPageProperties($platform, $version, $page)
            );
        } catch (InvalidArgumentException $e) {
            $resolvedPage = app(DocsVersionService::class)->resolvePageForVersion($platform, $version, $page);

            if ($resolvedPage !== $page && file_exists(resource_path("views/docs/{$platform}/{$version}/{$resolvedPage}.md"))) {
                return redirect(route('docs.show', [
                    'platform' => $platform,
                    'version' => $version,
                    'page' => $resolvedPage,
                ]), 301);
            }

            return $this->redirectToFirstNavigationPage($navigation, $page);
        }
        $title = $pageProperties['title'].' - NativePHP '.$platform.' v'.$version;
        $description = Arr::exists($pageProperties, 'description') ? $pageProperties['description'] : 'NativePHP documentation for '.$platform.' v'.$version;

        $canonicalUrl = app(DocsVersionService::class)->determineCanonicalUrl(
            platform: $platform,
            page: $page,
        );

        SEOMeta::setCanonical($canonicalUrl);

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);

        // Set OpenGraph metadata
        SEOTools::opengraph()->setTitle($pageProperties['title']);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::opengraph()->setType('article');

        // Set Twitter Card metadata
        SEOTools::twitter()->setTitle($pageProperties['title']);
        SEOTools::twitter()->setDescription($description);

        return view('docs.index')->with($pageProperties);
    }

    /**
     * Cache the callback's result for a day, or compute it fresh in local so
     * docs edits show up immediately without clearing (or racing on) the cache.
     * The key folds in `config('docs')` so a Jump version bump invalidates
     * rendered pages instead of trailing by up to a day.
     *
     * The entry also carries a fingerprint of the markdown it was built from
     * and is rebuilt as soon as that stops matching. Deploys ship new docs
     * without clearing the application cache, so the TTL alone leaves an
     * edited page — and the sidebar rendered alongside it — up to a day behind
     * the files. Entries written before the fingerprint existed have none, so
     * they miss and rebuild on the first request.
     */
    private function cacheOrCompute(string $key, string $fingerprint, Closure $callback): mixed
    {
        if (config('app.env') === 'local') {
            return $callback();
        }

        $key = $key.'_'.substr(md5(serialize(config('docs'))), 0, 8);

        $cached = Cache::get($key);

        if (Arr::get($cached, 'fingerprint') === $fingerprint) {
            return $cached['value'];
        }

        $value = $callback();

        Cache::put($key, ['fingerprint' => $fingerprint, 'value' => $value], now()->addDay());

        return $value;
    }

    /**
     * A signature of every markdown file behind a platform's version — names
     * and modification times, without reading any content.
     */
    private function fingerprint(string $platform, string $version): string
    {
        $files = (new Finder)
            ->files()
            ->name('*.md')
            ->in(resource_path("views/docs/{$platform}/{$version}"));

        return md5(collect($files)
            ->map(fn (SplFileInfo $file): string => $file->getRelativePathname().'@'.$file->getMTime())
            ->sort()
            ->implode('|'));
    }

    public function serveRawMarkdown(Request $request, string $platform, string $version, string $page)
    {
        abort_unless(is_dir(resource_path('views/docs/'.$platform.'/'.$version)), 404);

        $filePath = resource_path("views/docs/{$platform}/{$version}/{$page}.md");

        if (! file_exists($filePath)) {
            abort(404);
        }

        $content = file_get_contents($filePath);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'inline; filename="'.basename($filePath).'"',
        ]);
    }

    protected function getPageProperties($platform, $version, $page = null): array
    {
        $markdownFileName = $platform.'.'.$version.'.'.($page ?? 'index');

        $content = $this->getMarkdownView("docs.{$markdownFileName}", [
            'user' => auth()->user(),
        ])->render();

        $document = YamlFrontMatter::parse($content);
        $pageProperties = $document->matter();

        $versionProperties = YamlFrontMatter::parseFile(resource_path("views/docs/{$platform}/{$version}/_index.md"));

        $pageProperties = array_merge($versionProperties->matter(), $pageProperties);

        $pageProperties['platform'] = $platform;
        $pageProperties['version'] = $version;
        $pageProperties['pagePath'] = request()->path();

        $platformEnum = DocsPlatform::tryFrom($platform);
        $pageProperties['docsVersionLabels'] = $platformEnum
            ? app(DocsVersionRegistry::class)->switcherLabels($platformEnum)
            : [];

        $pageProperties['content'] = CommonMark::convertToHtml($document->body(), [
            'user' => auth()->user(),
        ]);
        $pageProperties['tableOfContents'] = $this->extractTableOfContents($pageProperties['content']);

        $navigation = app(DocsNavigationService::class)->build(DocsPlatform::from($platform), (int) $version);
        $pageProperties['navigation'] = Menu::build($navigation, function (Menu $menu, $nav): void {
            if (array_key_exists('path', $nav)) {
                $menu->link($nav['path'], $nav['title']);
            } elseif (array_key_exists('children', $nav)) {
                $menu->setItemParentAttribute('x-data', '{ open: $el.classList.contains(\'active\') }');

                // Find first navigable path (could be direct child or nested in subsection)
                $firstPath = $this->findFirstPath($nav['children']);

                $header = Html::raw('
                    <a href="'.$firstPath.'" class="flex items-center gap-2 justify-between" x-on:click.prevent="open = !open">
                        <span>'.$nav['title'].'</span>
                        <span class="text-gray-400 dark:text-gray-600">
                            <svg class="size-3 transition duration-300 will-change-transform ease-in-out" :class="open ? \'rotate-180\' : \'rotate-90\'" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </span>
                    </a>
                    ');

                $submenu = Menu::new()
                    ->setAttributes([
                        'x-show' => 'open',
                        'x-collapse' => '',
                    ]);
                foreach ($nav['children'] as $child) {
                    if (isset($child['is_subsection']) && $child['is_subsection']) {
                        // 3rd tier: subsection with its own children
                        // Check if any child page is active
                        $hasActivePage = collect($child['children'])->contains(fn ($c) => isset($c['path']) && $c['path'] === '/'.request()->path());

                        $firstChildPath = isset($child['children'][0]) ? $child['children'][0]['path'] : '#';
                        $subHeader = Html::raw('
                            <div x-data="{ subOpen: '.($hasActivePage ? 'true' : 'false').' }">
                            <a href="'.$firstChildPath.'" class="subsection-header" x-on:click.prevent="subOpen = !subOpen">
                                <span>'.$child['title'].'</span>
                                <span class="text-gray-400 dark:text-gray-600">
                                    <svg class="size-2.5 transition duration-300 will-change-transform ease-in-out" :class="subOpen ? \'rotate-180\' : \'rotate-90\'" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </span>
                            </a>
                            ');

                        $subSubmenu = Menu::new()
                            ->setAttributes([
                                'x-show' => 'subOpen',
                                'x-collapse' => '',
                                'class' => 'third-tier',
                            ]);

                        foreach ($child['children'] as $subChild) {
                            $subSubmenu->link($subChild['path'], $subChild['title']);
                        }

                        $subSubmenu->append('</div>');

                        $submenu->submenu($subHeader, $subSubmenu);
                    } else {
                        $submenu->link($child['path'], $child['title']);
                    }
                }

                $menu->submenu($header, $submenu);
            }
        })
            ->setActive(\request()->path())
            ->__toString();

        $pageProperties['editUrl'] = "https://github.com/NativePHP/nativephp.com/tree/main/resources/views/docs/{$platform}/{$version}/{$page}.md";

        // Find the next & previous page in the navigation
        $pageProperties['nextPage'] = null;
        $pageProperties['previousPage'] = null;

        // Flatten all navigable pages for prev/next calculation
        $flatPages = $this->flattenNavigationPages($navigation);
        $currentPath = '/'.$pageProperties['pagePath'];

        foreach ($flatPages as $index => $page) {
            if ($page['path'] === $currentPath) {
                if (isset($flatPages[$index + 1])) {
                    $pageProperties['nextPage'] = $flatPages[$index + 1];
                }
                if (isset($flatPages[$index - 1])) {
                    $pageProperties['previousPage'] = $flatPages[$index - 1];
                }
                break;
            }
        }

        if (isset($pageProperties['packageName'])) {
            $cardFilename = '/img/docs/'.strtolower(Str::slug($pageProperties['packageName'])).'/img/card.png';
            $cardPath = public_path($cardFilename);

            if (file_exists($cardPath)) {
                $pageProperties['socialCard'] = $cardFilename;
            }
        }

        return $pageProperties;
    }

    protected function findFirstPath(array $children): string
    {
        foreach ($children as $child) {
            if (isset($child['path'])) {
                return $child['path'];
            }
            if (isset($child['children']) && ! empty($child['children'])) {
                $path = $this->findFirstPath($child['children']);
                if ($path !== '#') {
                    return $path;
                }
            }
        }

        return '#';
    }

    protected function flattenNavigationPages(array $navigation): array
    {
        $pages = [];

        foreach ($navigation as $section) {
            if (isset($section['path'])) {
                $pages[] = $section;
            }
            if (isset($section['children'])) {
                foreach ($section['children'] as $child) {
                    if (isset($child['path'])) {
                        $pages[] = $child;
                    }
                    // Handle 3rd tier (subsections)
                    if (isset($child['is_subsection']) && isset($child['children'])) {
                        foreach ($child['children'] as $subChild) {
                            if (isset($subChild['path'])) {
                                $pages[] = $subChild;
                            }
                        }
                    }
                }
            }
        }

        return $pages;
    }

    protected function extractTableOfContents(string $html): array
    {
        if (! preg_match_all('/<(h[23])\s+id="([^"]+)"[^>]*>(.*?)<\/\1>/si', $html, $matches, PREG_SET_ORDER)) {
            return [];
        }

        return collect($matches)
            ->map(function (array $match) {
                return [
                    'level' => (int) $match[1][1],
                    'title' => html_entity_decode(trim(strip_tags(preg_replace('/<a\b[^>]*>.*?<\/a>/i', '', $match[3]))), ENT_QUOTES | ENT_HTML5),
                    'anchor' => $match[2],
                ];
            })
            ->values()
            ->toArray();
    }

    protected function markdownViewExists($platform, $version, $page): bool
    {
        $markdownFileName = $platform.'.'.$version.'.'.($page ?? 'index');

        try {
            $this->getMarkdownView("docs.{$markdownFileName}", [
                'user' => auth()->user(),
            ])->render();
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    protected function redirectToFirstNavigationPage(array $navigation, $page = null): RedirectResponse
    {
        $firstNavigationPath = collect($navigation)
            ->filter(function ($nav) use ($page) {
                if (! is_null($page)) {
                    return Arr::get($nav, 'relative_path') === $page;
                }

                return true;
            })
            ->filter(function ($nav) {
                return array_key_exists('path', $nav) || array_key_exists('children', $nav);
            })
            ->flatMap(function ($nav) {
                if (array_key_exists('path', $nav)) {
                    return $nav;
                }
                if (array_key_exists('children', $nav)) {
                    return $nav['children'];
                }

                return null;
            })
            ->first();

        if (is_null($firstNavigationPath) && ! is_null($page)) {
            return $this->redirectToFirstNavigationPage($navigation);
        }

        return is_string($firstNavigationPath) ? redirect($firstNavigationPath,
            301) : redirect($firstNavigationPath['path'], 301);
    }

    protected function getMarkdownView($view, array $data = [], array $mergeData = []): View
    {
        /** @var ViewFactory $factory */
        $factory = resolve(ViewFactory::class);

        $factory->addExtension('md', 'blade');

        return $factory->make($view, $data, $mergeData);
    }
}
