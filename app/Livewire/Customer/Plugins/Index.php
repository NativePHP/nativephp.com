<?php

namespace App\Livewire\Customer\Plugins;

use App\Enums\PluginStatus;
use App\Models\DeveloperAccount;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Your Plugins')]
class Index extends Component
{
    #[Url]
    public string $status = 'pending';

    #[Computed]
    public function plugins(): Collection
    {
        return auth()->user()->plugins()
            ->where('status', $this->status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function pluginCounts(): array
    {
        $counts = auth()->user()->plugins()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            PluginStatus::Approved->value => $counts[PluginStatus::Approved->value] ?? 0,
            PluginStatus::Pending->value => $counts[PluginStatus::Pending->value] ?? 0,
            PluginStatus::Rejected->value => $counts[PluginStatus::Rejected->value] ?? 0,
        ];
    }

    #[Computed]
    public function developerAccount(): ?DeveloperAccount
    {
        return auth()->user()->developerAccount;
    }

    public function render()
    {
        return view('livewire.customer.plugins.index');
    }
}
