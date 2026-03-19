<?php

namespace Tests\Feature\Notifications;

use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginReviewChecksIncomplete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginReviewChecksIncompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_shows_passing_and_failing_checks(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->create([
            'user_id' => $user->id,
            'name' => 'acme/test-plugin',
            'review_checks' => [
                'supports_ios' => true,
                'supports_android' => true,
                'supports_js' => false,
                'has_support_email' => true,
                'support_email' => 'dev@acme.io',
                'requires_mobile_sdk' => false,
                'mobile_sdk_constraint' => null,
                'has_ios_min_version' => true,
                'ios_min_version' => '18.0',
                'has_android_min_version' => false,
                'android_min_version' => null,
            ],
        ]);

        $notification = new PluginReviewChecksIncomplete($plugin);
        $rendered = $notification->toMail($user)->render()->toHtml();

        // Passing checks shown
        $this->assertStringContainsString('iOS native code', $rendered);
        $this->assertStringContainsString('Android native code', $rendered);
        $this->assertStringContainsString('Support email', $rendered);
        $this->assertStringContainsString('iOS min_version', $rendered);

        // Failing checks shown with doc links
        $this->assertStringContainsString('JavaScript library', $rendered);
        $this->assertStringContainsString('nativephp/mobile', $rendered);
        $this->assertStringContainsString('Android', $rendered);
        $this->assertStringContainsString('creating-plugins', $rendered);
        $this->assertStringContainsString('advanced-configuration', $rendered);
    }

    public function test_email_shows_all_failing_when_no_checks_pass(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->create([
            'user_id' => $user->id,
            'name' => 'acme/empty-plugin',
            'review_checks' => [
                'supports_ios' => false,
                'supports_android' => false,
                'supports_js' => false,
                'has_support_email' => false,
                'support_email' => null,
                'requires_mobile_sdk' => false,
                'mobile_sdk_constraint' => null,
                'has_ios_min_version' => false,
                'ios_min_version' => null,
                'has_android_min_version' => false,
                'android_min_version' => null,
            ],
        ]);

        $notification = new PluginReviewChecksIncomplete($plugin);
        $rendered = $notification->toMail($user)->render()->toHtml();

        $this->assertStringContainsString('bridge-functions', $rendered);
        $this->assertStringContainsString('creating-plugins', $rendered);
        $this->assertStringContainsString('best-practices', $rendered);
        $this->assertStringContainsString('advanced-configuration', $rendered);
    }

    public function test_email_subject_includes_plugin_name(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->create([
            'user_id' => $user->id,
            'name' => 'acme/cool-plugin',
            'review_checks' => [
                'supports_ios' => true,
                'supports_android' => true,
                'supports_js' => true,
                'has_support_email' => true,
                'support_email' => 'dev@acme.io',
                'requires_mobile_sdk' => true,
                'mobile_sdk_constraint' => '^3.0',
                'has_ios_min_version' => true,
                'ios_min_version' => '18.0',
                'has_android_min_version' => true,
                'android_min_version' => '33',
            ],
        ]);

        $notification = new PluginReviewChecksIncomplete($plugin);
        $mail = $notification->toMail($user);

        $this->assertEquals('Action Required: acme/cool-plugin — Review Checks', $mail->subject);
    }
}
