<?php

namespace App\Filament\Resources\SupportTicketResource\Widgets;

use App\Models\SupportTicket\Reply;
use App\Notifications\SupportTicketReplied;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Livewire\WithFileUploads;

class TicketRepliesWidget extends Widget
{
    use WithFileUploads;

    protected string $view = 'filament.resources.support-ticket-resource.widgets.ticket-replies';

    public ?Model $record = null;

    public string $newMessage = '';

    public bool $isNote = false;

    public array $replyAttachments = [];

    protected int|string|array $columnSpan = 'full';

    protected function getListeners(): array
    {
        return [];
    }

    public function sendReply(): void
    {
        $this->validate([
            'newMessage' => ['required', 'string', 'max:5000'],
            'replyAttachments' => ['array', 'max:5'],
            'replyAttachments.*' => ['file', 'max:10240'],
        ]);

        $attachments = null;

        if (! empty($this->replyAttachments)) {
            $attachments = [];

            foreach ($this->replyAttachments as $file) {
                $path = $file->store("{$this->record->mask}/replies", 'support-tickets');

                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        $reply = $this->record->replies()->create([
            'user_id' => auth()->id(),
            'message' => $this->newMessage,
            'note' => $this->isNote,
            'attachments' => $attachments,
        ]);

        if (! $this->isNote && $this->record->user_id !== auth()->id()) {
            $this->record->user->notify(new SupportTicketReplied($this->record, $reply));
        }

        $this->newMessage = '';
        $this->isNote = false;
        $this->replyAttachments = [];
    }

    public function removeReplyAttachment(int $index): void
    {
        $attachments = $this->replyAttachments;
        array_splice($attachments, $index, 1);
        $this->replyAttachments = $attachments;
    }

    public function togglePin(int $replyId): void
    {
        $reply = Reply::where('support_ticket_id', $this->record->id)
            ->where('id', $replyId)
            ->where('note', true)
            ->firstOrFail();

        if ($reply->pinned) {
            $reply->update(['pinned' => false]);
        } else {
            Reply::where('support_ticket_id', $this->record->id)
                ->where('pinned', true)
                ->update(['pinned' => false]);

            $reply->update(['pinned' => true]);
        }
    }
}
