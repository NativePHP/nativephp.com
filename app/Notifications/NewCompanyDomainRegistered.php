<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\CompanyDomainSynopsisGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCompanyDomainRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $domain,
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
        $synopsis = app(CompanyDomainSynopsisGenerator::class)->generate($this->user, $this->domain);

        $message = (new MailMessage)
            ->subject('New company domain: '.$this->domain)
            ->greeting('A new company domain just signed up.')
            ->line("**Domain:** {$this->domain}")
            ->line("**Name:** {$this->user->name}")
            ->line("**Email:** {$this->user->email}")
            ->line('**Signed up:** '.$this->user->created_at?->toDayDateTimeString());

        if (filled($synopsis)) {
            $message->line('**Synopsis:** '.$synopsis);
        }

        return $message;
    }
}
