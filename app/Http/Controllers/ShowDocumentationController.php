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

        $pageProperties['content'] = CommonMark::convertToHtml($document->body());
        $pageProperties['tableOfContents'] = $this->extractTableOfContents($document->body());

        $navigation = $this->getNavigation($platform, $version);
        $pageProperties['navigation'] = Menu::build($navigation, function (Menu $menu, $nav) {
            if (array_key_exists('path', $nav)) {
                $menu->link($nav['path'], $nav['title']);
            } elseif (array_key_exists('children', $nav)) {
                $menu->setItemParentAttribute('x-data', '{ open: $el.classList.contains(\'active\') }');

                $header = Html::raw('
                    <a href="'.$nav['children'][0]['path'].'" class="flex items-center gap-2 justify-between" x-on:click.prevent="open = !open">
                        <span>'.$nav['title'].'</span>
                        <span class="text-gray-400 dark:text-gray-600">
                            <svg class="size-3 transition duration-300 will-change-transform ease-in-out" :class="{\'rotate-180\': open,}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
                    $submenu->link($child['path'], $child['title']);
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

        foreach ($navigation as $i => $section) {
            foreach ($section['children'] as $key => $child) {
                if ($child['path'] === '/'.$pageProperties['pagePath']) {
                    if (isset($section['children'][$key + 1])) {
                        $pageProperties['nextPage'] = $section['children'][$key + 1];
                    } elseif (isset($navigation[$i + 1])) {
                        $navigation[$i + 1]['children'][0]['title'] = $navigation[$i + 1]['title'].': '.$navigation[$i + 1]['children'][0]['title'];
                        $pageProperties['nextPage'] = $navigation[$i + 1]['children'][0];
                    }

                    if (isset($section['children'][$key - 1])) {
                        $pageProperties['previousPage'] = $section['children'][$key - 1];
                    } elseif (isset($navigation[$i - 1])) {
                        $lastChild = count($navigation[$i - 1]['children']) - 1;
                        $navigation[$i - 1]['children'][$lastChild]['title'] = $navigation[$i - 1]['title'].': '.$navigation[$i - 1]['children'][$lastChild]['title'];
                        $pageProperties['previousPage'] = $navigation[$i - 1]['children'][$lastChild];
                    }
                }
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

            $navigationEntry['children'] = $children->sortBy('order')->values()->toArray();

            $navigation->push($navigationEntry);
        }

        return $navigation->sortBy('order')->values()->toArray();
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
