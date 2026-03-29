<?php

namespace App\Livewire\Customer\Support;

use App\Models\SupportTicket;
use App\Notifications\SupportTicketUserReplied;
use App\SupportTicket\Status;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Support Ticket')]
class Show extends Component
{
    public SupportTicket $supportTicket;

    public string $replyMessage = '';

    public function mount(SupportTicket $supportTicket): void
    {
        abort_unless(auth()->user()->hasUltraAccess(), 403);
        $this->authorize('view', $supportTicket);

        $supportTicket->load(['user', 'replies.user']);

        $this->supportTicket = $supportTicket;
    }

    public function reply(): void
    {
        $this->authorize('reply', $this->supportTicket);

        $this->validate([
            'replyMessage' => ['required', 'string', 'max:5000'],
        ]);

        $reply = $this->supportTicket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
            'note' => false,
        ]);

        Notification::route('mail', 'support@nativephp.com')
            ->notify(new SupportTicketUserReplied($this->supportTicket, $reply));

        $this->replyMessage = '';
        $this->supportTicket->load(['user', 'replies.user']);

        session()->flash('success', 'Your reply has been sent.');
    }

    public function closeTicket(): void
    {
        $this->authorize('closeTicket', $this->supportTicket);

        $this->supportTicket->update([
            'status' => Status::CLOSED,
        ]);

        session()->flash('success', __('account.support_ticket.close_ticket.success'));
    }

    public function render(): View
    {
        return view('livewire.customer.support.show');
    }
}
