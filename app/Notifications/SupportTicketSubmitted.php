<?php

namespace App\Notifications;

use App\Filament\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SupportTicketSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket->loadMissing('user');
        $user = $ticket->user;

        return (new MailMessage)
            ->subject('New Support Ticket: '.$ticket->subject)
            ->greeting('New support ticket received!')
            ->line("**Customer:** {$user->name}")
            ->line('**Email:** '.$this->obfuscateEmail($user->email))
            ->line("**Product:** {$ticket->product}")
            ->line('**Issue Type:** '.($ticket->issue_type ?? 'N/A'))
            ->line("**Subject:** {$ticket->subject}")
            ->line('**Message:**')
            ->line(Str::limit($ticket->message, 500))
            ->action('View Ticket', SupportTicketResource::getUrl('view', ['record' => $ticket]));
    }

    private function obfuscateEmail(string $email): string
    {
        $parts = explode('@', $email);
        $local = $parts[0];
        $domain = $parts[1] ?? '';

        $visibleLocal = Str::length($local) > 2
            ? Str::substr($local, 0, 2).str_repeat('*', Str::length($local) - 2)
            : $local;

        $domainParts = explode('.', $domain);
        $domainName = $domainParts[0] ?? '';
        $tld = implode('.', array_slice($domainParts, 1));

        $visibleDomain = Str::length($domainName) > 2
            ? Str::substr($domainName, 0, 2).str_repeat('*', Str::length($domainName) - 2)
            : $domainName;

        return "{$visibleLocal}@{$visibleDomain}.{$tld}";
    }
}
