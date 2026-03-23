<?php

namespace App\Filament\Resources\SupportTicketResource\Widgets;

use App\Notifications\SupportTicketReplied;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class TicketRepliesWidget extends Widget
{
    protected static string $view = 'filament.resources.support-ticket-resource.widgets.ticket-replies';

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

        if (! $this->isNote) {
            $this->record->user->notify(new SupportTicketReplied($this->record, $reply));
        }

        $this->newMessage = '';
        $this->isNote = false;
    }
}
