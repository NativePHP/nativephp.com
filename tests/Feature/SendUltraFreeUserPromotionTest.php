<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Models\License;
use App\Models\User;
use App\Notifications\UltraFreeUserPromotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\SubscriptionItem;
use Tests\TestCase;

class SendUltraFreeUserPromotionTest extends TestCase
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

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    public function test_sends_to_user_with_no_licenses_and_no_subscriptions(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->artisan('ultra:send-free-user-promo')
            ->expectsOutputToContain('Found 1 eligible user(s)')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraFreeUserPromotion::class);
    }

    public function test_skips_user_with_license(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createLegacyLicense($user, 'mini');

        $this->artisan('ultra:send-free-user-promo')
            ->expectsOutputToContain('Found 0 eligible user(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraFreeUserPromotion::class);
    }

    public function test_skips_user_with_active_subscription(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createActiveSubscription($user, Subscription::Pro->stripePriceId());

        $this->artisan('ultra:send-free-user-promo')
            ->expectsOutputToContain('Found 0 eligible user(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraFreeUserPromotion::class);
    }

    public function test_sends_to_multiple_eligible_users(): void
    {
        Notification::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Give user3 a license so they should be excluded
        $this->createLegacyLicense($user3, 'pro');

        $this->artisan('ultra:send-free-user-promo')
            ->expectsOutputToContain('Found 2 eligible user(s)')
            ->expectsOutputToContain('Sent: 2 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user1, UltraFreeUserPromotion::class);
        Notification::assertSentTo($user2, UltraFreeUserPromotion::class);
        Notification::assertNotSentTo($user3, UltraFreeUserPromotion::class);
    }

    public function test_dry_run_does_not_send(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->artisan('ultra:send-free-user-promo --dry-run')
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain("Would send to: {$user->email}")
            ->expectsOutputToContain('Would send: 1 email(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraFreeUserPromotion::class);
    }

    public function test_notification_has_correct_subject(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new UltraFreeUserPromotion;
        $mail = $notification->toMail($user);

        $this->assertEquals('NativePHP is Free — And Ultra Takes It Further', $mail->subject);
    }

    public function test_notification_greeting_uses_first_name(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new UltraFreeUserPromotion;
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi Jane,', $mail->greeting);
    }

    public function test_notification_greeting_fallback_when_no_name(): void
    {
        $user = User::factory()->create(['name' => null]);

        $notification = new UltraFreeUserPromotion;
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi there,', $mail->greeting);
    }

    public function test_notification_contains_ultra_benefits(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraFreeUserPromotion;
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('free and open source', $rendered);
        $this->assertStringContainsString('Teams', $rendered);
        $this->assertStringContainsString('Free official plugins', $rendered);
        $this->assertStringContainsString('Plugin Dev Kit', $rendered);
        $this->assertStringContainsString('Priority support', $rendered);
        $this->assertStringContainsString('Early access', $rendered);
        $this->assertStringContainsString('Exclusive content', $rendered);
        $this->assertStringContainsString('Shape the roadmap', $rendered);
        $this->assertStringContainsString('monthly billing', $rendered);
    }

    public function test_notification_mentions_nativephp_is_free(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraFreeUserPromotion;
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('free and open source', $rendered);
        $this->assertStringContainsString('no license required', $rendered);
    }
}
