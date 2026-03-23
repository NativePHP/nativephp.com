<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Models\User;
use App\Notifications\UltraUpgradePromotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendUltraUpgradePromotionTest extends TestCase
{
    use RefreshDatabase;

    private const COMPED_ULTRA_PRICE_ID = 'price_test_ultra_comped';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id_comped' => self::COMPED_ULTRA_PRICE_ID]);
    }

    private function createSubscription(User $user, string $priceId, bool $isComped = false): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => $priceId,
                'is_comped' => $isComped,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    public function test_sends_to_mini_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Mini->stripePriceId());

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('Found 1 eligible subscriber(s)')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_sends_to_pro_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Pro->stripePriceId());

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('Found 1 eligible subscriber(s)')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_skips_max_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Max->stripePriceId());

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('Found 0 eligible subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_skips_comped_ultra_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, self::COMPED_ULTRA_PRICE_ID);

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('Found 0 eligible subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_skips_comped_mini_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Mini->stripePriceId(), isComped: true);

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('Found 0 eligible subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_skips_comped_pro_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Pro->stripePriceId(), isComped: true);

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('Found 0 eligible subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_dry_run_does_not_send(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Mini->stripePriceId());

        $this->artisan('ultra:send-upgrade-promo --dry-run')
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain("Would send to: {$user->email}")
            ->expectsOutputToContain('Would send: 1 email(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, UltraUpgradePromotion::class);
    }

    public function test_notification_has_correct_subject(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new UltraUpgradePromotion('Mini');
        $mail = $notification->toMail($user);

        $this->assertEquals('Unlock More with NativePHP Ultra', $mail->subject);
    }

    public function test_notification_greeting_uses_first_name(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new UltraUpgradePromotion('Mini');
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi Jane,', $mail->greeting);
    }

    public function test_notification_greeting_fallback_when_no_name(): void
    {
        $user = User::factory()->create(['name' => null]);

        $notification = new UltraUpgradePromotion('Mini');
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi there,', $mail->greeting);
    }

    public function test_notification_contains_current_plan_name(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraUpgradePromotion('Mini');
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('Mini', $rendered);
    }

    public function test_notification_contains_ultra_benefits(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraUpgradePromotion('Pro');
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

    public function test_notification_mentions_prorated_billing(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new UltraUpgradePromotion('Mini');
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('prorated', $rendered);
    }

    public function test_personalizes_plan_name_for_mini(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Mini->stripePriceId());

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('(Mini)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraUpgradePromotion::class, function ($notification) {
            return $notification->currentPlanName === 'Mini';
        });
    }

    public function test_personalizes_plan_name_for_pro(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createSubscription($user, Subscription::Pro->stripePriceId());

        $this->artisan('ultra:send-upgrade-promo')
            ->expectsOutputToContain('(Pro)')
            ->assertSuccessful();

        Notification::assertSentTo($user, UltraUpgradePromotion::class, function ($notification) {
            return $notification->currentPlanName === 'Pro';
        });
    }
}
