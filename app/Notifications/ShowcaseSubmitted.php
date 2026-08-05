<?php

namespace App\Notifications;

use App\Filament\Resources\ShowcaseResource;
use App\Models\Showcase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ShowcaseSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Showcase $showcase,
        public bool $resubmitted = false,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $showcase = $this->showcase->loadMissing('user');
        $user = $showcase->user;

        $platforms = collect([
            $showcase->has_mobile ? 'Mobile' : null,
            $showcase->has_desktop ? 'Desktop' : null,
        ])->filter()->implode(', ');

        $subject = $this->resubmitted
            ? 'Showcase Re-submitted for Review: '.$showcase->title
            : 'New Showcase Submission: '.$showcase->title;

        $greeting = $this->resubmitted
            ? 'An approved showcase has been updated and sent back for review.'
            : 'A new app has been submitted to the Showcase!';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line("**Title:** {$showcase->title}")
            ->line('**Submitted by:** '.($user?->name ?? 'Unknown'))
            ->when($platforms !== '', fn (MailMessage $message) => $message->line("**Platforms:** {$platforms}"))
            ->line('**Description:**')
            ->line(Str::limit($showcase->description, 500))
            ->action('Review Showcase', ShowcaseResource::getUrl('edit', ['record' => $showcase]));
    }
}
