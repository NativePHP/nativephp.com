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
use Spatie\Menu\Link;
use Spatie\Menu\Menu;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ShowDocumentationController extends Controller
{
    public function __invoke(Request $request, string $version, string $page = null)
    {
        if (config('app.env') === 'local') {
            Cache::flush();
        }

        abort_unless(is_dir(resource_path('views/docs/'.$version)), 404);

        session(['viewing_docs_version' => $version]);

        $navigation = Cache::remember("docs_nav_{$version}", now()->addDay(), function () use ($version) {
            return $this->getNavigation($version);
        });

        if (is_null($page)) {
            return $this->redirectToFirstNavigationPage($navigation);
        }

        try {
            $pageProperties = Cache::remember("docs_{$version}_{$page}", now()->addDay(), function () use ($version, $page) {
                return $this->getPageProperties($version, $page);
            });
        } catch (InvalidArgumentException $e) {
            return $this->redirectToFirstNavigationPage($navigation, $page);
        }

        SEOTools::setTitle($pageProperties['title']);
        SEOTools::setDescription(Arr::exists($pageProperties, 'description') ? $pageProperties['description'] : '');

        return view('docs.index')->with($pageProperties);
    }

    protected function getPageProperties($version, $page = null): array
    {
        $markdownFileName = $version.'.'.($page ?? 'index');

        $content = $this->getMarkdownView("docs.{$markdownFileName}", [
            'user' => auth()->user(),
        ])->render();

        $document = YamlFrontMatter::parse($content);
        $pageProperties = $document->matter();

        $versionProperties = YamlFrontMatter::parseFile(resource_path("views/docs/{$version}/_index.md"));
        $pageProperties = array_merge($pageProperties, $versionProperties->matter());

        $pageProperties['version'] = $version;
        $pageProperties['pagePath'] = request()->path();

        $pageProperties['content'] = CommonMark::convertToHtml($document->body(), $version);
        $pageProperties['tableOfContents'] = $this->extractTableOfContents($document->body());

        $navigation = $this->getNavigation($version);
        $pageProperties['navigation'] = Menu::build($navigation, function (Menu $menu, $nav) {
            if (array_key_exists('path', $nav)) {
                $menu->link($nav['path'], $nav['title']);
            } elseif (array_key_exists('children', $nav)) {
                $menu->submenu(Link::to($nav['children'][0]['path'], $nav['title']), function (Menu $submenu) use ($nav) {
                    foreach ($nav['children'] as $child) {
                        $submenu->link($child['path'], $child['title']);
                    }
                });
            }
        })
            ->setActive(\request()->path())
            ->__toString();

        if (isset($pageProperties['packageName'])) {
            $cardFilename = '/img/docs/'.strtolower(Str::slug($pageProperties['packageName'])).'/img/card.png';
            $cardPath = public_path($cardFilename);

            if (file_exists($cardPath)) {
                $pageProperties['socialCard'] = $cardFilename;
            }
        }

        return $pageProperties;
    }

    protected function getNavigation(string $version): array
    {
        $basePath = resource_path('views');
        $path = "$basePath/docs/$version";

        $mainNavigation = (new Finder())
            ->files()
            ->name('_index.md')
            ->depth(1)
            ->in($path);

        $navigation = collect();

        $mainPages = (new Finder())
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
                'path'  => $path,
                'title' => $parsedSection->matter('title', ''),
                'order' => $parsedSection->matter('order', 0),
            ]);
        }

        /** @var SplFileInfo $section */
        foreach ($mainNavigation as $section) {
            $parsedSection = YamlFrontMatter::parse($section->getContents());
            $navigationEntry = [
                'relative_path' => $section->getRelativePath(),
                'title'         => $parsedSection->matter('title', ''),
                'order'         => $parsedSection->matter('order', 0),
            ];

            $subSections = (new Finder())
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
                    'path'  => $path,
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
        $document = preg_replace('(```[a-z]*\n[\s\S]*?\n```)', '', $document);

        return collect(explode(PHP_EOL, $document))
            ->reject(function (string $line) {
                // Only search for level 2 and 3 headings.
                return ! Str::startsWith($line, '## ') && ! Str::startsWith($line, '### ');
            })
            ->map(function (string $line) {
                return [
                    'level' => strlen(trim(Str::before($line, '# '))) + 1,
                    'title' => $title = trim(Str::after($line, '# ')),
                    'anchor' => Str::slug($title),
                ];
            })
            ->toArray();
    }

    protected function extractTitle(string $document): string
    {
        $matches = [];

        preg_match('/<h1>([^<]+)/', $document, $matches);

        return $matches[1] ?? '';
    }

    protected function markdownViewExists($version, $page): bool
    {
        $markdownFileName = $version.'.'.($page ?? 'index');

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
                if (!is_null($page)) {
                    return Arr::get($nav, 'relative_path') === $page;
                }

                return true;
            })
            ->filter(function ($nav) {
                return array_key_exists('path', $nav) || array_key_exists('children', $nav);
            })
            ->map(function ($nav) {
                if (array_key_exists('path', $nav)) {
                    return $nav;
                }
                if (array_key_exists('children', $nav)) {
                    return $nav['children'];
                }

                return null;
            })
            ->flatten(1)
            ->first();

        if (is_null($firstNavigationPath) && !is_null($page)) {
            return $this->redirectToFirstNavigationPage($navigation);
        }

        return is_string($firstNavigationPath) ? redirect($firstNavigationPath, 301) : redirect($firstNavigationPath['path'], 301);
    }

    protected function getMarkdownView($view, array $data = [], array $mergeData = []): View
    {
        /** @var ViewFactory $factory */
        $factory = app(ViewFactory::class);

        $factory->addExtension('md', 'blade');

        return $factory->make($view, $data, $mergeData);
    }
}
