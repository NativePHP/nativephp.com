<?php

namespace Tests\Feature;

use App\Ai\Agents\CompanyDomainAnalyst;
use App\Models\User;
use App\Notifications\NewCompanyDomainRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class NewCompanyDomainRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Lovelace',
            'email' => 'ada@acme.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_first_company_domain_signup_emails_accounts(): void
    {
        Notification::fake();
        config(['services.turnstile.secret_key' => null]);

        $response = $this->post('/register', $this->registrationPayload());

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        Notification::assertSentOnDemand(
            NewCompanyDomainRegistered::class,
            function (NewCompanyDomainRegistered $notification, array $channels, object $notifiable): bool {
                return $notifiable->routes['mail'] === 'accounts@nativephp.com'
                    && $notification->domain === 'acme.com'
                    && $notification->user->email === 'ada@acme.com';
            }
        );
    }

    public function test_second_user_on_same_company_domain_does_not_email_accounts(): void
    {
        Notification::fake();
        config(['services.turnstile.secret_key' => null]);

        User::factory()->create(['email' => 'first@acme.com']);

        $response = $this->post('/register', $this->registrationPayload([
            'email' => 'second@acme.com',
        ]));

        $response->assertRedirect(route('dashboard'));

        Notification::assertSentOnDemandTimes(NewCompanyDomainRegistered::class, 0);
    }

    public function test_gmail_signup_does_not_email_accounts(): void
    {
        Notification::fake();
        config(['services.turnstile.secret_key' => null]);

        $response = $this->post('/register', $this->registrationPayload([
            'email' => 'person@gmail.com',
        ]));

        $response->assertRedirect(route('dashboard'));

        Notification::assertSentOnDemandTimes(NewCompanyDomainRegistered::class, 0);
    }

    public function test_accounts_email_includes_ai_synopsis_when_enrichment_succeeds(): void
    {
        Notification::fake();
        config(['services.turnstile.secret_key' => null]);

        Http::fake([
            'acme.com/*' => Http::response(
                '<html><head><title>Acme</title></head><body><h1>Acme Corp</h1><p>We build industrial widgets for manufacturers worldwide.</p></body></html>',
                200
            ),
            'acme.com' => Http::response(
                '<html><head><title>Acme</title></head><body><h1>Acme Corp</h1><p>We build industrial widgets for manufacturers worldwide.</p></body></html>',
                200
            ),
        ]);

        $synopsis = 'Acme Corp builds industrial widgets for manufacturers. Ada Lovelace appears to be an early registrant from that company domain.';

        CompanyDomainAnalyst::fake([$synopsis]);

        $response = $this->post('/register', $this->registrationPayload());

        $response->assertRedirect(route('dashboard'));

        Notification::assertSentOnDemandTimes(NewCompanyDomainRegistered::class, 1);

        $notification = null;
        $notifiable = null;

        Notification::assertSentOnDemand(
            NewCompanyDomainRegistered::class,
            function (NewCompanyDomainRegistered $sent, array $channels, object $route) use (&$notification, &$notifiable): bool {
                $notification = $sent;
                $notifiable = $route;

                return $route->routes['mail'] === 'accounts@nativephp.com'
                    && $sent->domain === 'acme.com';
            }
        );

        $mail = $notification->toMail($notifiable);

        CompanyDomainAnalyst::assertPrompted(function ($prompt) {
            return $prompt->contains('ada@acme.com')
                && $prompt->contains('acme.com')
                && $prompt->contains('industrial widgets');
        });

        $this->assertTrue(
            collect($mail->introLines)->contains(
                fn (string $line): bool => str_contains($line, '**Synopsis:** '.$synopsis)
            )
        );

        Http::assertSent(fn ($request) => str_contains($request->url(), 'acme.com'));
    }

    public function test_accounts_email_still_sent_when_ai_synopsis_fails(): void
    {
        Notification::fake();
        config(['services.turnstile.secret_key' => null]);

        Http::fake([
            'acme.com/*' => Http::response('<html><body>Acme widgets</body></html>', 200),
            'acme.com' => Http::response('<html><body>Acme widgets</body></html>', 200),
        ]);

        CompanyDomainAnalyst::fake(fn () => throw new RuntimeException('provider down'));

        $response = $this->post('/register', $this->registrationPayload());

        $response->assertRedirect(route('dashboard'));

        $notification = null;
        $notifiable = null;

        Notification::assertSentOnDemand(
            NewCompanyDomainRegistered::class,
            function (NewCompanyDomainRegistered $sent, array $channels, object $route) use (&$notification, &$notifiable): bool {
                $notification = $sent;
                $notifiable = $route;

                return $route->routes['mail'] === 'accounts@nativephp.com'
                    && $sent->domain === 'acme.com';
            }
        );

        $mail = $notification->toMail($notifiable);

        $this->assertTrue(
            collect($mail->introLines)->contains(
                fn (string $line): bool => str_contains($line, '**Domain:** acme.com')
            )
        );

        $this->assertTrue(
            collect($mail->introLines)->doesntContain(
                fn (string $line): bool => str_contains($line, '**Synopsis:**')
            )
        );
    }
}
