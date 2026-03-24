<?php

namespace Tests\Feature\Auth;

use App\Features\ShowAuthButtons;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    // --- Login page ---

    public function test_login_page_renders(): void
    {
        $response = $this->withoutVite()->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in to your account');
        $response->assertSee('create a new account');
        $response->assertSee('Sign in with GitHub');
    }

    public function test_login_page_shows_status_session_message(): void
    {
        $response = $this->withoutVite()->get('/login', ['status' => 'test']);

        $response->assertStatus(200);
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials_shows_error(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('dashboard'));
    }

    // --- Register page ---

    public function test_register_page_renders(): void
    {
        $response = $this->withoutVite()->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create your account');
        $response->assertSee('Terms of Service');
        $response->assertSee('Privacy Policy');
        $response->assertSee('Sign up with GitHub');
    }

    public function test_register_creates_user_and_logs_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'testuser@gmail.com', 'name' => 'Test User']);
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->from('/register')->post('/register', []);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_register_validates_password_confirmation(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
    }

    public function test_register_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
    }

    // --- Forgot password page ---

    public function test_forgot_password_page_renders(): void
    {
        $response = $this->withoutVite()->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Reset your password');
        $response->assertSee('Back to login');
    }

    public function test_forgot_password_sends_reset_link(): void
    {
        $user = User::factory()->create(['email' => 'resettest@gmail.com']);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status');
    }

    public function test_forgot_password_validates_email(): void
    {
        $response = $this->from('/forgot-password')->post('/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect('/forgot-password');
        $response->assertSessionHasErrors('email');
    }

    // --- Reset password page ---

    public function test_reset_password_page_renders(): void
    {
        $response = $this->withoutVite()->get('/reset-password/test-token');

        $response->assertStatus(200);
        $response->assertSee('Set new password');
    }

    public function test_reset_password_updates_password(): void
    {
        $user = User::factory()->create(['email' => 'resetpw@gmail.com']);

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect(route('customer.login'));
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_reset_password_validates_password_confirmation(): void
    {
        $user = User::factory()->create();

        $token = Password::createToken($user);

        $response = $this->from("/reset-password/{$token}")->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
