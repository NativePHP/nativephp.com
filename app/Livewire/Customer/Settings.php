<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Settings')]
class Settings extends Component
{
    public string $name = '';

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPassword_confirmation = '';

    public string $deleteConfirmPassword = '';

    public function mount(): void
    {
        $this->name = auth()->user()->name ?? '';
    }

    public function updateName(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->update(['name' => $this->name]);

        session()->flash('name-updated', 'Your name has been updated.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset('currentPassword', 'newPassword', 'newPassword_confirmation');

        session()->flash('password-updated', 'Your password has been updated.');
    }

    public function deleteAccount(): void
    {
        $this->validate([
            'deleteConfirmPassword' => ['required', 'current_password'],
        ]);

        $user = auth()->user();

        // Cancel active subscription immediately
        $subscription = $user->subscription();

        if ($subscription && $subscription->active()) {
            $subscription->cancelNow();
        }

        // Delete related records that lack cascade foreign keys
        $user->licenses()->delete();
        $user->subscriptions()->delete();

        Auth::guard('web')->logout();

        $user->delete();

        $this->redirect(route('welcome'));
    }
}
