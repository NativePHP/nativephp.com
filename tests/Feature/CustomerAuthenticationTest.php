<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class CustomerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable the auth feature flag for all authentication tests
        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_customer_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in to your account');
        $response->assertSee('create a new account');
    }

    public function test_customer_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_customer_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_customer_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Reset your password');
    }

    public function test_authenticated_customer_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    public function test_unauthenticated_customer_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_password_reset_email_is_sent_for_unverified_users(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'email' => 'unverified-customer@gmail.com',
        ]);

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_email_is_sent_for_opted_out_users(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'opted-out-customer@gmail.com',
            'receives_notification_emails' => false,
        ]);

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_verifies_email_when_unverified(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'claimer@gmail.com',
        ]);
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect('/login');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }

    public function test_password_reset_preserves_existing_verification_timestamp(): void
    {
        $verifiedAt = now()->subMonth()->startOfDay();
        $user = User::factory()->create([
            'email' => 'already-verified@gmail.com',
            'email_verified_at' => $verifiedAt,
        ]);
        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $user->refresh();
        $this->assertTrue($user->email_verified_at->equalTo($verifiedAt));
    }
}
