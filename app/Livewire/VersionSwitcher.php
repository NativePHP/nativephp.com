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
        return redirect()->route('docs.show', [
            'platform' => $this->platform,
            'version' => $this->version,
            'page' => $this->page,
        ]);
    }
}
