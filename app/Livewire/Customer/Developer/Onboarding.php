<?php

namespace App\Livewire\Customer\Developer;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Developer Onboarding')]
class Onboarding extends Component
{
    public function mount(): void
    {
        $developerAccount = auth()->user()->developerAccount;

        if ($developerAccount && $developerAccount->hasCompletedOnboarding()) {
            $this->redirect(route('customer.developer.dashboard'), navigate: true);
            session()->flash('message', 'Your developer account is already set up.');
        }
    }

    #[Computed]
    public function developerAccount()
    {
        return auth()->user()->developerAccount;
    }

    #[Computed]
    public function hasExistingAccount(): bool
    {
        return $this->developerAccount !== null;
    }

    public function render()
    {
        return view('livewire.customer.developer.onboarding');
    }
}
