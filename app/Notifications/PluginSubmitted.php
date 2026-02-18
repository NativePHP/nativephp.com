<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PluginSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Plugin $plugin
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
        $message = (new MailMessage)
            ->subject('Plugin Submitted: '.$this->plugin->name)
            ->greeting('Thanks for submitting your plugin!')
            ->line("We've received your submission for **{$this->plugin->name}** and it's now in our review queue.")
            ->line('When the NativePHP team is ready to review your plugin, they may reach out with questions. Once we approve or reject your submission, we\'ll notify you by email.');

        $failingChecks = $this->getFailingChecks();

        if (count($failingChecks) > 0) {
            $message->line('**Review Checks**')
                ->line('We ran some automated checks against your repository. The following items could use your attention before we review:');

            foreach ($failingChecks as $check) {
                $message->line("- {$check}");
            }

            $message->line('Updating your repository with these items will help speed up the review process. We\'ll re-run these checks when we begin our review.');
        }

        $message->action('View Your Plugin', route('customer.plugins.show', $this->plugin->routeParams()))
            ->salutation("Happy coding!\n\nThe NativePHP Team");

        return $message;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plugin_id' => $this->plugin->id,
            'plugin_name' => $this->plugin->name,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getFailingChecks(): array
    {
        $checks = $this->plugin->review_checks;

        if (! $checks) {
            return [];
        }

        $labels = [
            'supports_ios' => 'Add iOS support (resources/ios/)',
            'supports_android' => 'Add Android support (resources/android/)',
            'supports_js' => 'Add JavaScript support (resources/js/)',
            'has_support_email' => 'Add a support email to your README',
            'requires_mobile_sdk' => 'Require the nativephp/mobile SDK in composer.json',
        ];

        $failing = [];

        foreach ($labels as $key => $label) {
            if (empty($checks[$key])) {
                $failing[] = $label;
            }
        }

        return $failing;
    }
}
