<?php

namespace App\Livewire\Customer\Showcase;

use App\Models\Showcase;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Edit Submission')]
class Edit extends Component
{
    public Showcase $showcase;

    public function mount(Showcase $showcase): void
    {
        abort_if($showcase->user_id !== auth()->id(), 403);
    }
}
