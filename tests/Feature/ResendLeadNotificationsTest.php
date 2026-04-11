<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Notifications\NewLeadSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResendLeadNotificationsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resends_notifications_for_leads_from_the_given_date(): void
    {
        Notification::fake();

        $oldLead = Lead::factory()->create(['created_at' => '2025-01-01 12:00:00']);
        $matchingLead = Lead::factory()->create(['created_at' => '2025-03-15 09:00:00']);
        $newerLead = Lead::factory()->create(['created_at' => '2025-03-16 14:00:00']);

        $this->artisan('app:resend-lead-notifications', ['date' => '2025-03-15'])
            ->expectsOutputToContain('Found 2 lead(s)')
            ->expectsOutputToContain('Done.')
            ->assertExitCode(0);

        Notification::assertSentOnDemand(
            NewLeadSubmitted::class,
            function ($notification, $channels, $notifiable) use ($matchingLead) {
                return $notifiable->routes['mail'] === 'sales@nativephp.com'
                    && $notification->lead->is($matchingLead);
            }
        );

        Notification::assertSentOnDemand(
            NewLeadSubmitted::class,
            function ($notification, $channels, $notifiable) use ($newerLead) {
                return $notifiable->routes['mail'] === 'sales@nativephp.com'
                    && $notification->lead->is($newerLead);
            }
        );

        Notification::assertNotSentTo($oldLead, NewLeadSubmitted::class);
    }

    #[Test]
    public function it_shows_message_when_no_leads_are_found(): void
    {
        Notification::fake();

        $this->artisan('app:resend-lead-notifications', ['date' => '2099-01-01'])
            ->expectsOutputToContain('No leads found')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
