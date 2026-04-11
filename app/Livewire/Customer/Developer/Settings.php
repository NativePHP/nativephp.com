<?php

namespace App\Livewire\Customer\Developer;

use App\Models\DeveloperAccount;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Developer Settings')]
class Settings extends Component
{
    #[Validate('nullable|string|max:255')]
    public ?string $displayName = null;

    public function mount(): void
    {
        $this->displayName = auth()->user()->display_name;
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
}
