<?php

namespace App\Livewire\Customer\Plugins;

use App\Models\DeveloperAccount;
use App\Models\Plugin;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Your Plugins')]
class Index extends Component
{
    #[Validate('nullable|string|max:255')]
    public ?string $displayName = null;

    public function mount(): void
    {
        $this->displayName = auth()->user()->display_name;
    }

    #[Computed]
    public function plugins(): Collection
    {
        return auth()->user()->plugins()->orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function developerAccount(): ?DeveloperAccount
    {
        return auth()->user()->developerAccount;
    }

    public function updateDisplayName(): void
    {
        $this->validate();

        auth()->user()->update([
            'display_name' => $this->displayName ?: null,
        ]);

        session()->flash('success', 'Display name updated successfully!');
    }

    public function resubmitPlugin(int $pluginId): void
    {
        $plugin = Plugin::findOrFail($pluginId);

        if ($plugin->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $plugin->isRejected()) {
            session()->flash('error', 'Only rejected plugins can be resubmitted.');

            return;
        }

        $plugin->resubmit();

        session()->flash('success', 'Your plugin has been resubmitted for review!');
    }

    public function render()
    {
        return view('livewire.customer.plugins.index');
    }
}
