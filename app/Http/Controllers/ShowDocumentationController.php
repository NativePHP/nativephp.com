<?php

namespace App\Http\Controllers;

use App\Support\CommonMark\CommonMark;
use Artesaos\SEOTools\Facades\SEOTools;
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
        if (config('app.env') === 'local') {
            Cache::flush();
        }

        abort_unless(is_dir(resource_path('views/docs/'.$platform.'/'.$version)), 404);

        session(['viewing_docs_version' => $version]);
        session(['viewing_docs_platform' => $platform]);

        $navigation = Cache::remember("docs_nav_{$platform}_{$version}", now()->addDay(),
            fn () => $this->getNavigation($platform, $version)
        );

        if (is_null($page)) {
            return $this->redirectToFirstNavigationPage($navigation);
        }

        try {
            $pageProperties = Cache::remember("docs_{$platform}_{$version}_{$page}", now()->addDay(),
                fn () => $this->getPageProperties($platform, $version, $page)
            );
        } catch (InvalidArgumentException $e) {
            return $this->redirectToFirstNavigationPage($navigation, $page);
        }
        $title = $pageProperties['title'].' - NativePHP '.$platform.' v'.$version;
        $description = Arr::exists($pageProperties, 'description') ? $pageProperties['description'] : 'NativePHP documentation for '.$platform.' v'.$version;

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

        $pageProperties['content'] = CommonMark::convertToHtml($document->body(), [
            'user' => auth()->user(),
        ]);
        $pageProperties['tableOfContents'] = $this->extractTableOfContents($document->body());

        $navigation = $this->getNavigation($platform, $version);
        $pageProperties['navigation'] = Menu::build($navigation, function (Menu $menu, $nav) {
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

    protected function getNavigation(string $platform, string $version): array
    {
        $basePath = resource_path('views');
        $path = "$basePath/docs/$platform/$version";

        $mainNavigation = (new Finder)
            ->files()
            ->name('_index.md')
            ->depth(1)
            ->in($path);

        $navigation = collect();

        $mainPages = (new Finder)
            ->files()
            ->notName('_index.md')
            ->name('*.md')
            ->depth(0)
            ->in($path);

        /** @var SplFileInfo $mainPage */
        foreach ($mainPages as $mainPage) {
            $parsedSection = YamlFrontMatter::parse($mainPage->getContents());

            $path = Str::after($mainPage->getPath(), $basePath).'/'.$mainPage->getBasename('.md');

            $navigation->push([
                'path' => $path,
                'title' => $parsedSection->matter('title', ''),
                'order' => $parsedSection->matter('order', 0),
            ]);
        }

        /** @var SplFileInfo $section */
        foreach ($mainNavigation as $section) {
            $parsedSection = YamlFrontMatter::parse($section->getContents());
            $navigationEntry = [
                'relative_path' => $section->getRelativePath(),
                'title' => $parsedSection->matter('title', ''),
                'order' => $parsedSection->matter('order', 0),
            ];

            // Get direct child pages (depth 0)
            $subSections = (new Finder)
                ->files()
                ->notName('_index.md')
                ->name('*.md')
                ->depth(0)
                ->in($section->getPath());

            $children = collect();

            /** @var SplFileInfo $subSection */
            foreach ($subSections as $subSection) {
                $parsedSection = YamlFrontMatter::parse($subSection->getContents());

                $path = Str::after($subSection->getPath(), $basePath).'/'.$subSection->getBasename('.md');

                $title = $parsedSection->matter('title', '');

                if ($title === '') {
                    $content = CommonMark::convertToHtml($subSection->getContents());
                    $title = $this->extractTitle($content);
                }

                $children->push([
                    'path' => $path,
                    'title' => $title,
                    'order' => $parsedSection->matter('order', 0),
                ]);
            }

            // Check for nested subsections (3rd tier)
            $nestedSections = (new Finder)
                ->files()
                ->name('_index.md')
                ->depth(1)
                ->in($section->getPath());

            /** @var SplFileInfo $nestedSection */
            foreach ($nestedSections as $nestedSection) {
                $parsedNested = YamlFrontMatter::parse($nestedSection->getContents());

                $nestedEntry = [
                    'title' => $parsedNested->matter('title', ''),
                    'order' => $parsedNested->matter('order', 0),
                    'is_subsection' => true,
                ];

                // Get pages within this nested section
                $nestedPages = (new Finder)
                    ->files()
                    ->notName('_index.md')
                    ->name('*.md')
                    ->depth(0)
                    ->in($nestedSection->getPath());

                $nestedChildren = collect();

                /** @var SplFileInfo $nestedPage */
                foreach ($nestedPages as $nestedPage) {
                    $parsedPage = YamlFrontMatter::parse($nestedPage->getContents());

                    $path = Str::after($nestedPage->getPath(), $basePath).'/'.$nestedPage->getBasename('.md');

                    $title = $parsedPage->matter('title', '');

                    if ($title === '') {
                        $content = CommonMark::convertToHtml($nestedPage->getContents());
                        $title = $this->extractTitle($content);
                    }

                    $nestedChildren->push([
                        'path' => $path,
                        'title' => $title,
                        'order' => $parsedPage->matter('order', 0),
                    ]);
                }

                $nestedEntry['children'] = $nestedChildren->sortBy('order')->values()->toArray();
                $children->push($nestedEntry);
            }

            $navigationEntry['children'] = $children->sortBy('order')->values()->toArray();

            $navigation->push($navigationEntry);
        }

        return $navigation->sortBy('order')->values()->toArray();
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

    protected function extractTableOfContents(string $document): array
    {
        // Remove code blocks which might contain headers.
        $document = preg_replace('/```[a-z]*\s(.*?)```/s', '', $document);

        return collect(explode(PHP_EOL, $document))
            ->reject(function (string $line) {
                // Only search for level 2 and 3 headings.
                return ! Str::startsWith($line, '## ') && ! Str::startsWith($line, '### ');
            })
            ->map(function (string $line) {
                return [
                    'level' => strlen(trim(Str::before($line, '# '))) + 1,
                    'title' => $title = htmlspecialchars_decode(trim(Str::after($line, '# '))),
                    'anchor' => Str::slug(Str::replace('`', 'code', $title)),
                ];
            })
            ->values()
            ->toArray();
    }

    protected function extractTitle(string $document): string
    {
        $matches = [];

        preg_match('/<h1>([^<]+)/', $document, $matches);

        return $matches[1] ?? '';
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
        $factory = app(ViewFactory::class);

        $factory->addExtension('md', 'blade');

        return $factory->make($view, $data, $mergeData);
    }
}
