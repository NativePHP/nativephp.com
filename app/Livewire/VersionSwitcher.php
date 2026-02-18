<?php

namespace App\Livewire;

use Illuminate\View\ViewException;
use Livewire\Component;

class VersionSwitcher extends Component
{
    public string $platform;

    public array $versions;

    public int $version;

    public string $page;

    public function mount(array $versions)
    {
        // Since the props are always bound to the uri we fetch them
        // from the route instead of prop drilling or adding
        // single-use view data to the controller

        throw_unless(
            request()->route()->named('docs.show'),
            ViewException::class,
            "The version switcher can only be used on the 'docs.show' route."
        );

        $this->platform = request()->route()->parameter('platform');
        $this->version = request()->route()->parameter('version');
        $this->page = request()->route()->parameter('page');
        $this->versions = $versions;
    }

    public function updatedVersion()
    {
        if (! $this->pageExists($this->platform, $this->version, $this->page)) {
            $this->page = 'introduction';
        }

        return to_route('docs.show', [
            'platform' => $this->platform,
            'version' => $this->version,
            'page' => $this->page,
        ]);
    }

    protected function pageExists(string $platform, int $version, string $page): bool
    {
        return file_exists(resource_path("views/docs/{$platform}/{$version}/{$page}.md"));
    }
}
