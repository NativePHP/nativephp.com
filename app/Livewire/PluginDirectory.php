<?php

namespace App\Livewire;

use App\Enums\PluginCategory;
use App\Enums\PluginType;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layout')]
#[Title('Plugin Marketplace')]
class PluginDirectory extends Component
{
    use WithPagination;

    /**
     * Filter value representing plugins with no `category` recorded.
     */
    public const string CATEGORY_UNCATEGORIZED = 'uncategorized';

    /**
     * Filter value representing plugins with no `mobile_min_version` recorded.
     */
    public const string MOBILE_VERSION_UNSPECIFIED = 'unspecified';

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $author = null;

    #[Url]
    public string $view = 'plugins';

    #[Url]
    public string $type = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $mobileVersion = '';

    /**
     * Drop any `type`/`category` value that isn't a real enum case (a stale
     * bookmark, a hand-edited URL) instead of crashing later on `::from()`.
     */
    public function mount(): void
    {
        if ($this->type !== '' && PluginType::tryFrom($this->type) === null) {
            $this->type = '';
        }

        if ($this->category !== ''
            && $this->category !== self::CATEGORY_UNCATEGORIZED
            && PluginCategory::tryFrom($this->category) === null) {
            $this->category = '';
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedAuthor(): void
    {
        $this->resetPage();
    }

    public function updatedView(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedMobileVersion(): void
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

    public function clearType(): void
    {
        $this->type = '';
        $this->resetPage();
    }

    public function clearCategory(): void
    {
        $this->category = '';
        $this->resetPage();
    }

    public function clearMobileVersion(): void
    {
        $this->mobileVersion = '';
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->type = '';
        $this->category = '';
        $this->mobileVersion = '';
        $this->resetPage();
    }

    public function showPlugins(): void
    {
        $this->view = 'plugins';
        $this->resetPage();
    }

    public function showBundles(): void
    {
        $this->view = 'bundles';
        $this->resetPage();
    }

    public function render(): View
    {
        $user = Auth::user();
        $authorUser = $this->author ? User::find($this->author) : null;

        $plugins = Plugin::query()
            ->approved()
            ->where('is_active', true)
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $q): void {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->author, function (Builder $query): void {
                $query->where('user_id', $this->author);
            })
            ->when($this->type !== '', function (Builder $query): void {
                $query->where('type', $this->type);
            })
            ->when($this->category !== '', function (Builder $query): void {
                if ($this->category === self::CATEGORY_UNCATEGORIZED) {
                    $query->whereNull('category');

                    return;
                }

                $query->where('category', $this->category);
            })
            ->when($this->mobileVersion !== '', function (Builder $query): void {
                if ($this->mobileVersion === self::MOBILE_VERSION_UNSPECIFIED) {
                    $query->whereNull('mobile_min_version');

                    return;
                }

                $query->where(function (Builder $q): void {
                    $q->where('mobile_min_version', 'like', "{$this->mobileVersion}.%")
                        ->orWhere('mobile_min_version', $this->mobileVersion);
                });
            })
            ->orderByDesc('featured')
            ->latest()
            ->paginate(12);

        $bundles = PluginBundle::query()
            ->active()
            ->with('plugins')
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $q): void {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get()
            ->filter(fn (PluginBundle $bundle) => $bundle->hasAccessiblePriceFor($user));

        return view('livewire.plugin-directory', [
            'plugins' => $plugins,
            'bundles' => $bundles,
            'authorUser' => $authorUser,
            'typeOptions' => PluginType::cases(),
            'categoryOptions' => PluginCategory::cases(),
            'mobileVersionOptions' => config('plugins.mobile_major_versions', []),
            'categoryUncategorizedValue' => self::CATEGORY_UNCATEGORIZED,
            'mobileVersionUnspecifiedValue' => self::MOBILE_VERSION_UNSPECIFIED,
            'typeLabel' => $this->type !== '' ? PluginType::from($this->type)->label() : null,
            'categoryLabel' => $this->categoryLabel(),
            'mobileVersionLabel' => $this->mobileVersionLabel(),
        ]);
    }

    protected function categoryLabel(): ?string
    {
        return match (true) {
            $this->category === '' => null,
            $this->category === self::CATEGORY_UNCATEGORIZED => 'Uncategorized',
            default => PluginCategory::from($this->category)->label(),
        };
    }

    protected function mobileVersionLabel(): ?string
    {
        return match (true) {
            $this->mobileVersion === '' => null,
            $this->mobileVersion === self::MOBILE_VERSION_UNSPECIFIED => 'Version unspecified',
            default => "v{$this->mobileVersion}.x and up",
        };
    }
}
