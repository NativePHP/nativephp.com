<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Models\User;
use App\Notifications\MaxToUltraAnnouncement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendMaxToUltraAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    private const COMPED_ULTRA_PRICE_ID = 'price_test_ultra_comped';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id_comped' => self::COMPED_ULTRA_PRICE_ID]);
    }

    private function createPaidMaxSubscription(User $user, ?string $priceId = null): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $price = $priceId ?? Subscription::Max->stripePriceId();

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => $price,
                'is_comped' => false,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => $price,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createCompedMaxSubscription(User $user): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'is_comped' => true,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createProSubscription(User $user): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => Subscription::Pro->stripePriceId(),
                'is_comped' => false,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Pro->stripePriceId(),
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createCompedUltraSubscription(User $user): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => self::COMPED_ULTRA_PRICE_ID,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => self::COMPED_ULTRA_PRICE_ID,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    public function test_sends_to_paying_max_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);

        $this->artisan('ultra:send-announcement')
            ->expectsOutputToContain('Found 1 paying Max subscriber(s)')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, MaxToUltraAnnouncement::class);
    }

    public function test_sends_to_monthly_max_subscriber(): void
    {
        Notification::fake();

        $monthlyPriceId = config('subscriptions.plans.max.stripe_price_id_monthly');

        if (! $monthlyPriceId) {
            $this->markTestSkipped('Monthly Max price ID not configured');
        }

        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user, $monthlyPriceId);

        $this->artisan('ultra:send-announcement')
            ->expectsOutputToContain('Sent: 1 email(s)')
            ->assertSuccessful();

        Notification::assertSentTo($user, MaxToUltraAnnouncement::class);
    }

    public function test_skips_comped_max_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createCompedMaxSubscription($user);

        $this->artisan('ultra:send-announcement')
            ->expectsOutputToContain('Found 0 paying Max subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, MaxToUltraAnnouncement::class);
    }

    public function test_skips_comped_ultra_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createCompedUltraSubscription($user);

        $this->artisan('ultra:send-announcement')
            ->expectsOutputToContain('Found 0 paying Max subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, MaxToUltraAnnouncement::class);
    }

    public function test_skips_pro_subscriber(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createProSubscription($user);

        $this->artisan('ultra:send-announcement')
            ->expectsOutputToContain('Found 0 paying Max subscriber(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, MaxToUltraAnnouncement::class);
    }

    public function test_dry_run_does_not_send(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);

        $this->artisan('ultra:send-announcement --dry-run')
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain("Would send to: {$user->email}")
            ->expectsOutputToContain('Would send: 1 email(s)')
            ->assertSuccessful();

        Notification::assertNotSentTo($user, MaxToUltraAnnouncement::class);
    }

    public function test_notification_has_correct_subject(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new MaxToUltraAnnouncement;
        $mail = $notification->toMail($user);

        $this->assertEquals('Your Max Plan is Now NativePHP Ultra', $mail->subject);
    }

    public function test_notification_greeting_uses_first_name(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $notification = new MaxToUltraAnnouncement;
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi Jane,', $mail->greeting);
    }

    public function test_notification_greeting_fallback_when_no_name(): void
    {
        $user = User::factory()->create(['name' => null]);

        $notification = new MaxToUltraAnnouncement;
        $mail = $notification->toMail($user);

        $this->assertEquals('Hi there,', $mail->greeting);
    }

    public function test_notification_contains_ultra_benefits(): void
    {
        $user = User::factory()->create(['name' => 'Test']);

        $notification = new MaxToUltraAnnouncement;
        $mail = $notification->toMail($user);

        $rendered = $mail->render()->__toString();

        $this->assertStringContainsString('Teams', $rendered);
        $this->assertStringContainsString('Free official plugins', $rendered);
        $this->assertStringContainsString('Priority support', $rendered);
        $this->assertStringContainsString('Early access', $rendered);
        $this->assertStringContainsString('Exclusive content', $rendered);
        $this->assertStringContainsString('Shape the roadmap', $rendered);
    }
}
