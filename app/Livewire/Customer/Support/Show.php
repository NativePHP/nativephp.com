<?php

namespace App\Livewire\Customer\Support;

use App\Models\SupportTicket;
use App\Notifications\SupportTicketUserReplied;
use App\SupportTicket\Status;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Support Ticket')]
class Show extends Component
{
    use WithFileUploads;

    public SupportTicket $supportTicket;

    public string $replyMessage = '';

    public array $replyAttachments = [];

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

        $key = 'support-reply:'.auth()->id();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError('replyMessage', "You're sending messages too quickly. Please wait {$seconds} seconds.");

            return;
        }

        $this->validate([
            'replyMessage' => ['required', 'string', 'max:5000'],
            'replyAttachments' => ['array', 'max:5'],
            'replyAttachments.*' => ['file', 'max:10240'],
        ]);

        RateLimiter::hit($key, 60);

        $attachments = null;

        if (! empty($this->replyAttachments)) {
            $attachments = [];

            foreach ($this->replyAttachments as $file) {
                $path = $file->store("{$this->supportTicket->mask}/replies", 'support-tickets');

                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        $reply = $this->supportTicket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
            'note' => false,
            'attachments' => $attachments,
        ]);

        Notification::route('mail', 'support@nativephp.com')
            ->notify(new SupportTicketUserReplied($this->supportTicket, $reply));

        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->supportTicket->load(['user', 'replies.user']);
    }

    public function closeTicket(): void
    {
        $this->authorize('closeTicket', $this->supportTicket);

        $this->supportTicket->update([
            'status' => Status::CLOSED,
        ]);

        $this->supportTicket->replies()->create([
            'user_id' => null,
            'message' => auth()->user()->name.' closed this ticket.',
            'note' => false,
        ]);

        $this->supportTicket->load(['user', 'replies.user']);
    }

    public function removeReplyAttachment(int $index): void
    {
        $attachments = $this->replyAttachments;
        array_splice($attachments, $index, 1);
        $this->replyAttachments = $attachments;
    }

    public function reopenTicket(): void
    {
        $this->authorize('reopenTicket', $this->supportTicket);

        $this->supportTicket->update([
            'status' => Status::OPEN,
        ]);

        $this->supportTicket->replies()->create([
            'user_id' => null,
            'message' => auth()->user()->name.' reopened this ticket.',
            'note' => false,
        ]);

        $this->supportTicket->load(['user', 'replies.user']);
    }

    public function render(): View
    {
        return view('livewire.customer.support.show');
    }
}
