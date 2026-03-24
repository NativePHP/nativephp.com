<?php

namespace App\Livewire\Customer\Licenses;

use App\Models\SubLicense;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Your Licenses')]
class Index extends Component
{
    #[Computed]
    public function licenses(): Collection
    {
        return auth()->user()->licenses()->orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function assignedSubLicenses(): Collection
    {
        $user = auth()->user();

        return SubLicense::query()
            ->with('parentLicense')
            ->where('assigned_email', $user->email)
            ->whereHas('parentLicense', function ($query) use ($user): void {
                $query->where('user_id', '!=', $user->id);
            })->latest()
            ->get();
    }
}
