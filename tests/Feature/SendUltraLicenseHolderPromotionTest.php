<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Models\License;
use App\Models\User;
use App\Notifications\UltraLicenseHolderPromotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendUltraLicenseHolderPromotionTest extends TestCase
{
    use RefreshDatabase;

    private function createLegacyLicense(User $user, string $policyName = 'mini'): License
    {
        return License::factory()
            ->for($user)
            ->withoutSubscriptionItem()
            ->state(['policy_name' => $policyName])
            ->create();
    }

    private function createActiveSubscription(User $user, string $priceId): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => $priceId,
                'is_comped' => false,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    public function test_sends_to_legacy_mini_license_holder(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'mini');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('Found 1 eligible license holder(s)')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraLicenseHolderPromotion::class);
    }

    public function test_sends_to_legacy_pro_license_holder(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'pro');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('Found 1 eligible license holder(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraLicenseHolderPromotion::class);
    }

    public function test_sends_to_legacy_max_license_holder(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'max');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('Found 1 eligible license holder(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraLicenseHolderPromotion::class);
    }

    public function test_skips_user_with_active_subscription(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'pro');
        $this->createActiveSubscription($user, Subscription::Pro->stripePriceId());

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain("Skipping {$user->email} - already has active subscription")
            ->expectsOutputToContain('Found 0 eligible license holder(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraLicenseHolderPromotion::class);
    }

    public function test_skips_license_tied_to_subscription_item(): void
    {
        Notification::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        // License with a subscription_item_id (not legacy)
        License::factory()
            ->for($user)
            ->state(['policy_name' => 'pro'])
            ->create();

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('Found 0 eligible license holder(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraLicenseHolderPromotion::class);
    }

    public function test_sends_only_one_email_per_user_with_multiple_licenses(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'mini');
        $this->createLegacyLicense($user, 'pro');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('Found 1 eligible license holder(s)')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentToTimes($user, UltraLicenseHolderPromotion::class, 1);
    }

    public function test_dry_run_does_not_send(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'mini');

        $this->artisan('ultra:send-license-holder-promo --dry-run')
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain("Would send to: {$user->email}")
            ->expectsOutputToContain('Would send: 1 email(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraLicenseHolderPromotion::class);
    }

    public function test_notification_has_correct_subject(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new UltraLicenseHolderPromotion('Mini');
        $mail = $notification->toMail($user);

        $this->assertEquals('Unlock More with NativePHP Ultra', $mail->subject);
    }

    public function test_notification_greeting_uses_first_name(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new UltraLicenseHolderPromotion('Mini');
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi Jane,', $mail->greeting);
    }

    public function test_notification_greeting_fallback_when_no_name(): void
    {
        $user = User::factory()->create(['name' => null]);

        $notification = new UltraLicenseHolderPromotion('Mini');
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi there,', $mail->greeting);
    }

    public function test_notification_contains_plan_name(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraLicenseHolderPromotion('Pro');
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('Pro', $rendered);
    }

    public function test_notification_contains_ultra_benefits(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraLicenseHolderPromotion('Mini');
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('Teams', $rendered);
        $this->assertStringContainsString('Free official plugins', $rendered);
        $this->assertStringContainsString('Plugin Dev Kit', $rendered);
        $this->assertStringContainsString('Priority support', $rendered);
        $this->assertStringContainsString('Early access', $rendered);
        $this->assertStringContainsString('Exclusive content', $rendered);
        $this->assertStringContainsString('Shape the roadmap', $rendered);
        $this->assertStringContainsString('monthly billing', $rendered);
    }

    public function test_personalizes_plan_name_for_mini(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'mini');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('(Mini)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraLicenseHolderPromotion::class, function ($notification) {
            return $notification->planName === 'Mini';
        });
    }

    public function test_personalizes_plan_name_for_pro(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'pro');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('(Pro)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraLicenseHolderPromotion::class, function ($notification) {
            return $notification->planName === 'Pro';
        });
    }

    public function test_personalizes_plan_name_for_max(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'max');

        $this->artisan('ultra:send-license-holder-promo')
            ->expectsOutputToContain('(Ultra)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraLicenseHolderPromotion::class, function ($notification) {
            return $notification->planName === 'Ultra';
        });
    }
}
