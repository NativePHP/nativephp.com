<?php

namespace App\Livewire;

use App\Models\Plugin;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layout')]
#[Title('Plugin Directory')]
class PluginDirectory extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $author = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedAuthor(): void
    {
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function clearAuthor(): void
    {
        $this->author = null;
        $this->resetPage();
    }

    public function render(): View
    {
        $authorUser = $this->author ? User::find($this->author) : null;

        $plugins = Plugin::query()
            ->approved()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->author, function ($query) {
                $query->where('user_id', $this->author);
            })
            ->orderByDesc('featured')
            ->latest()
            ->paginate(15);

        return view('livewire.plugin-directory', [
            'plugins' => $plugins,
            'authorUser' => $authorUser,
        ]);
    }
}
