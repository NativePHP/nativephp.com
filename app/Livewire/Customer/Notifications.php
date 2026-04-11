<?php

namespace App\Livewire\Customer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Notifications')]
class Notifications extends Component
{
    use WithPagination;

    #[Computed]
    public function notifications(): LengthAwarePaginator
    {
        return auth()->user()->notifications()->latest()->paginate(20);
    }

    public function markAsRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }
}
