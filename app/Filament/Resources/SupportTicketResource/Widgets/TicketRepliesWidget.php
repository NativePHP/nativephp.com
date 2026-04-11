<?php

namespace App\Filament\Resources\SupportTicketResource\Widgets;

use App\Models\SupportTicket\Reply;
use App\Notifications\SupportTicketReplied;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class TicketRepliesWidget extends Widget
{
    protected string $view = 'filament.resources.support-ticket-resource.widgets.ticket-replies';

    public ?Model $record = null;

    public string $newMessage = '';

    public bool $isNote = false;

    protected int|string|array $columnSpan = 'full';

    protected function getListeners(): array
    {
        return [];
    }

    public function sendReply(): void
    {
        $this->validate([
            'newMessage' => ['required', 'string', 'max:5000'],
        ]);

        $reply = $this->record->replies()->create([
            'user_id' => auth()->id(),
            'message' => $this->newMessage,
            'note' => $this->isNote,
        ]);

        if (! $this->isNote && $this->record->user_id !== auth()->id()) {
            $this->record->user->notify(new SupportTicketReplied($this->record, $reply));
        }

        $this->newMessage = '';
        $this->isNote = false;
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
