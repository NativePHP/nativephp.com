<?php

namespace Tests\Feature\Auth;

use App\Features\ShowAuthButtons;
use App\Livewire\Customer\Dashboard;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_registration_dispatches_registered_event(): void
    {
        Event::fake([Registered::class]);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'newuser@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        Event::assertDispatched(Registered::class, function (Registered $event) {
            return $event->user->email === 'newuser@gmail.com';
        });
    }

    public function test_registration_sends_verification_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'verifyuser@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'verifyuser@gmail.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_link_marks_user_as_verified(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Your email address has been verified.');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_resend_endpoint_sends_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'A new verification link has been sent to your email address.');
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify/'.$user->id.'/'.sha1($user->getEmailForVerification()).'?signature=invalid');

        $response->assertStatus(403);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_wrong_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email@example.com')]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertStatus(403);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_dashboard_banner_visible_for_unverified_users(): void
    {
        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->withoutLazyLoading()
            ->test(Dashboard::class)
            ->assertSee('Please verify your email address.');
    }

    public function test_dashboard_banner_hidden_for_verified_users(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->withoutLazyLoading()
            ->test(Dashboard::class)
            ->assertDontSee('Please verify your email address.');
    }

    public function test_already_verified_user_clicking_verify_link_is_handled_gracefully(): void
    {
        $user = User::factory()->create(); // already verified

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_notice_redirects_to_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_unauthenticated_user_cannot_access_verification_routes(): void
    {
        $response = $this->post(route('verification.send'));

        $response->assertRedirect(route('customer.login'));
    }
}
