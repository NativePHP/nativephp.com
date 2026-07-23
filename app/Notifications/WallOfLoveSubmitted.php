<?php

namespace App\Notifications;

use App\Filament\Resources\WallOfLoveSubmissionResource;
use App\Models\WallOfLoveSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class WallOfLoveSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WallOfLoveSubmission $submission
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
        $submission = $this->submission->loadMissing('user');
        $user = $submission->user;

        return (new MailMessage)
            ->subject('New Wall of Love Submission from '.$submission->name)
            ->greeting('A new Wall of Love submission has been received!')
            ->line("**Name:** {$submission->name}")
            ->line('**Submitted by:** '.($user?->name ?? 'Unknown'))
            ->when($submission->company, fn (MailMessage $message) => $message->line("**Company:** {$submission->company}"))
            ->when($submission->url, fn (MailMessage $message) => $message->line("**URL:** {$submission->url}"))
            ->when($submission->testimonial, fn (MailMessage $message) => $message->line('**Testimonial:**')->line(Str::limit($submission->testimonial, 500)))
            ->action('Review Submission', WallOfLoveSubmissionResource::getUrl('edit', ['record' => $submission]));
    }
}
