<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewCompanyDomainRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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
}
