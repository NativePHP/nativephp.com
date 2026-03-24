<?php

namespace App\Livewire\Customer\Support;

use App\Models\SupportTicket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Support Tickets')]
class Index extends Component
{
    use WithPagination;

    #[Computed]
    public function supportTickets(): LengthAwarePaginator
    {
        return SupportTicket::where('user_id', auth()->id())
            ->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.customer.support.index');
    }
}
