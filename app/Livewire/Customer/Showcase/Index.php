<?php

namespace App\Livewire\Customer\Showcase;

use App\Models\Showcase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Your Showcase Submissions')]
class Index extends Component
{
    #[Computed]
    public function showcases(): Collection
    {
        return Showcase::where('user_id', auth()->id())->latest()->get();
    }

    public function render(): View
    {
        return view('livewire.customer.showcase.index');
    }
}
