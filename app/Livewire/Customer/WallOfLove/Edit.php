<?php

namespace App\Livewire\Customer\WallOfLove;

use App\Models\WallOfLoveSubmission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Edit Your Listing')]
class Edit extends Component
{
    public WallOfLoveSubmission $wallOfLoveSubmission;

    public function mount(WallOfLoveSubmission $wallOfLoveSubmission): void
    {
        abort_if($wallOfLoveSubmission->user_id !== auth()->id(), 403);
    }
}
