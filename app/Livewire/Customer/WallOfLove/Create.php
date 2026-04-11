<?php

namespace App\Livewire\Customer\WallOfLove;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Join our Wall of Love')]
class Create extends Component
{
    public function mount(): void
    {
        $user = auth()->user();

        // Check if user is eligible (has early adopter license)
        $hasEarlyAdopterLicense = $user->licenses()
            ->where('created_at', '<', '2025-06-01')
            ->exists();

        if (! $hasEarlyAdopterLicense) {
            abort(404);
        }

        // Check if user already has a submission
        if ($user->wallOfLoveSubmissions()->exists()) {
            $this->redirect(route('dashboard'), navigate: true);
            session()->flash('info', 'You have already submitted your story to the Wall of Love.');
        }
    }
}
