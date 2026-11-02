<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationTurnstileTest extends TestCase
{
    use RefreshDatabase;

    private const SITE_KEY = '0x4AAAAAAEXlWKKRnwD5mM54';

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'turnstile-user@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cf-turnstile-response' => 'a-token',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function fakeSiteverify(array $overrides = []): void
    {
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(array_merge([
                'success' => true,
                'action' => 'register',
                'hostname' => 'nativephp.com',
            ], $overrides)),
        ]);
    }

    // --- Widget rendering ---

    public function test_register_page_renders_the_widget_when_a_site_key_is_configured(): void
    {
        config(['services.turnstile.site_key' => self::SITE_KEY]);

        $response = $this->withoutVite()->get('/register');

        $response->assertStatus(200);
        $response->assertSee('challenges.cloudflare.com/turnstile/v0/api.js', false);
        $response->assertSee('cf-turnstile', false);
        $response->assertSee('data-sitekey="'.self::SITE_KEY.'"', false);
        $response->assertSee('data-action="register"', false);
    }

    public function test_register_page_omits_the_widget_when_no_site_key_is_configured(): void
    {
        config(['services.turnstile.site_key' => null]);

        $response = $this->withoutVite()->get('/register');

        $response->assertStatus(200);
        $response->assertDontSee('challenges.cloudflare.com/turnstile/v0/api.js', false);
        $response->assertDontSee('cf-turnstile', false);
    }

    // --- Server-side verification ---

    public function test_registration_is_unguarded_when_no_secret_key_is_configured(): void
    {
        config(['services.turnstile.secret_key' => null]);

        Http::fake();

        $response = $this->post('/register', $this->registrationPayload(['cf-turnstile-response' => null]));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        Http::assertNothingSent();
    }

    public function test_registration_succeeds_with_a_verified_token(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);
        $this->fakeSiteverify();

        $response = $this->post('/register', $this->registrationPayload());

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'turnstile-user@gmail.com']);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'test-secret'
            && $request['response'] === 'a-token');
    }

    public function test_registration_is_rejected_when_the_token_is_missing(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);

        Http::fake();

        $response = $this->from('/register')->post('/register', $this->registrationPayload([
            'cf-turnstile-response' => null,
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['cf-turnstile-response' => 'Please complete the security check.']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
        Http::assertNothingSent();
    }

    public function test_registration_is_rejected_when_siteverify_fails(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);
        $this->fakeSiteverify(['success' => false, 'error-codes' => ['invalid-input-response']]);

        $response = $this->from('/register')->post('/register', $this->registrationPayload());

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_is_rejected_when_the_token_was_minted_for_another_form(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);
        $this->fakeSiteverify(['action' => 'lead-submission']);

        $response = $this->from('/register')->post('/register', $this->registrationPayload());

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_closed_when_cloudflare_is_unreachable(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);

        Http::fake(function (): void {
            throw new ConnectionException('Connection timed out');
        });

        $response = $this->from('/register')->post('/register', $this->registrationPayload());

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_closed_when_siteverify_returns_a_server_error(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);

        Http::fake(['challenges.cloudflare.com/*' => Http::response('', 500)]);

        $response = $this->from('/register')->post('/register', $this->registrationPayload());

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();
    }

    public function test_an_oversized_token_is_rejected_without_calling_cloudflare(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);

        Http::fake();

        $response = $this->from('/register')->post('/register', $this->registrationPayload([
            'cf-turnstile-response' => str_repeat('a', 2049),
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();
        Http::assertNothingSent();
    }

    // --- Optional hostname pinning ---

    public function test_registration_succeeds_when_the_hostname_is_on_the_allow_list(): void
    {
        config([
            'services.turnstile.secret_key' => 'test-secret',
            'services.turnstile.hostnames' => 'nativephp.com, www.nativephp.com',
        ]);
        $this->fakeSiteverify(['hostname' => 'www.nativephp.com']);

        $response = $this->post('/register', $this->registrationPayload());

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_registration_is_rejected_when_the_hostname_is_not_on_the_allow_list(): void
    {
        config([
            'services.turnstile.secret_key' => 'test-secret',
            'services.turnstile.hostnames' => 'nativephp.com',
        ]);
        $this->fakeSiteverify(['hostname' => 'phishing.example.com']);

        $response = $this->from('/register')->post('/register', $this->registrationPayload());

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
