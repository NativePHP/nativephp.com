<?php

namespace App\Notifications;

use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PluginReviewChecksIncomplete extends Notification implements ShouldQueue
{
    use Queueable;

    private const DOCS_BASE = 'https://nativephp.com/docs/mobile/3/plugins';

    /**
     * @var array<string, array{label: string, passing_label: string, docs_url: string, docs_label: string}>
     */
    private const CHECK_DEFINITIONS = [
        'supports_ios' => [
            'label' => 'iOS native code in `resources/ios/Sources/`',
            'passing_label' => 'iOS native code',
            'docs_url' => self::DOCS_BASE.'/bridge-functions',
            'docs_label' => 'Bridge Functions guide',
        ],
        'supports_android' => [
            'label' => 'Android native code in `resources/android/src/`',
            'passing_label' => 'Android native code',
            'docs_url' => self::DOCS_BASE.'/bridge-functions',
            'docs_label' => 'Bridge Functions guide',
        ],
        'supports_js' => [
            'label' => 'JavaScript library in `resources/js/`',
            'passing_label' => 'JavaScript library',
            'docs_url' => self::DOCS_BASE.'/creating-plugins',
            'docs_label' => 'Creating Plugins guide',
        ],
        'has_support_email' => [
            'label' => 'Support email in your README',
            'passing_label' => 'Support email',
            'docs_url' => self::DOCS_BASE.'/best-practices',
            'docs_label' => 'Best Practices guide',
        ],
        'requires_mobile_sdk' => [
            'label' => '`nativephp/mobile` required in `composer.json`',
            'passing_label' => 'Requires nativephp/mobile',
            'docs_url' => self::DOCS_BASE.'/creating-plugins',
            'docs_label' => 'Creating Plugins guide',
        ],
        'has_ios_min_version' => [
            'label' => 'iOS `min_version` set in `nativephp.json`',
            'passing_label' => 'iOS min_version',
            'docs_url' => self::DOCS_BASE.'/advanced-configuration',
            'docs_label' => 'Advanced Configuration guide',
        ],
        'has_android_min_version' => [
            'label' => 'Android `min_version` set in `nativephp.json`',
            'passing_label' => 'Android min_version',
            'docs_url' => self::DOCS_BASE.'/advanced-configuration',
            'docs_label' => 'Advanced Configuration guide',
        ],
    ];

    public function __construct(public Plugin $plugin) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $checks = $this->plugin->review_checks ?? [];
        $passing = $this->getPassingChecks($checks);
        $failing = $this->getFailingChecks($checks);

        $message = (new MailMessage)
            ->subject('Action Required: '.$this->plugin->name.' — Review Checks')
            ->greeting('Hello,')
            ->line("We've run automated checks against your plugin **{$this->plugin->name}** and found some items that need your attention before we can approve it.");

        if (count($passing) > 0) {
            $message->line('**Passing checks:**');

            foreach ($passing as $item) {
                $message->line("✅ {$item}");
            }
        }

        if (count($failing) > 0) {
            $message->line('**Missing items:**');

            foreach ($failing as $item) {
                $message->line("❌ {$item['label']} — [{$item['docs_label']}]({$item['docs_url']})");
            }
        }

        $message
            ->line('Please update your repository to address the missing items above. Once updated, we\'ll re-run the checks automatically.')
            ->action('View Best Practices', self::DOCS_BASE.'/best-practices')
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
    private function getPassingChecks(array $checks): array
    {
        $passing = [];

        foreach (self::CHECK_DEFINITIONS as $key => $definition) {
            if (! empty($checks[$key])) {
                $passing[] = $definition['passing_label'];
            }
        }

        return $passing;
    }

    /**
     * @return array<int, array{label: string, docs_url: string, docs_label: string}>
     */
    private function getFailingChecks(array $checks): array
    {
        $failing = [];

        foreach (self::CHECK_DEFINITIONS as $key => $definition) {
            if (empty($checks[$key])) {
                $failing[] = [
                    'label' => $definition['label'],
                    'docs_url' => $definition['docs_url'],
                    'docs_label' => $definition['docs_label'],
                ];
            }
        }

        return $failing;
    }
}
