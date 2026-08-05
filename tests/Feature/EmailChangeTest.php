<?php

namespace Tests\Feature;

use App\Jobs\SyncStripeCustomerDetailsJob;
use App\Livewire\Customer\Settings;
use App\Models\EmailChange;
use App\Models\TeamUser;
use App\Models\User;
use App\Notifications\EmailChangeCompleted;
use App\Notifications\EmailChangeRequested;
use App\Notifications\VerifyNewEmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class EmailChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_an_email_change(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', 'new@example.com')
            ->set('emailChangePassword', 'password')
            ->call('requestEmailChange')
            ->assertHasNoErrors()
            ->assertSee('new@example.com');

        $emailChange = EmailChange::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($emailChange);
        $this->assertSame($user->email, $emailChange->old_email);
        $this->assertSame('new@example.com', $emailChange->new_email);
        $this->assertNull($emailChange->confirmed_at);
        $this->assertTrue($emailChange->expires_at->between(now()->addMinutes(59), now()->addMinutes(61)));

        $this->assertSame($user->email, $user->fresh()->email);

        Notification::assertSentOnDemand(
            VerifyNewEmailAddress::class,
            fn (VerifyNewEmailAddress $notification, array $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === 'new@example.com'
        );

        Notification::assertSentTo($user, EmailChangeRequested::class);
    }

    public function test_email_change_request_requires_correct_password(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', 'new@example.com')
            ->set('emailChangePassword', 'wrong-password')
            ->call('requestEmailChange')
            ->assertHasErrors(['emailChangePassword']);

        $this->assertDatabaseCount('email_changes', 0);
        Notification::assertNothingSent();
    }

    public function test_email_change_request_rejects_taken_email(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', $otherUser->email)
            ->set('emailChangePassword', 'password')
            ->call('requestEmailChange')
            ->assertHasErrors(['newEmail']);

        $this->assertDatabaseCount('email_changes', 0);
    }

    public function test_email_change_request_rejects_current_email(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', strtoupper($user->email))
            ->set('emailChangePassword', 'password')
            ->call('requestEmailChange')
            ->assertHasErrors(['newEmail']);

        $this->assertDatabaseCount('email_changes', 0);
    }

    public function test_email_change_request_rejects_invalid_email(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', 'not-an-email')
            ->set('emailChangePassword', 'password')
            ->call('requestEmailChange')
            ->assertHasErrors(['newEmail']);

        $this->assertDatabaseCount('email_changes', 0);
    }

    public function test_new_request_supersedes_previous_pending_request(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $previous = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'first@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', 'second@example.com')
            ->set('emailChangePassword', 'password')
            ->call('requestEmailChange')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('email_changes', ['id' => $previous->id]);
        $this->assertDatabaseCount('email_changes', 1);
        $this->assertDatabaseHas('email_changes', [
            'user_id' => $user->id,
            'new_email' => 'second@example.com',
        ]);
    }

    public function test_email_change_requests_are_rate_limited(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $component = Livewire::actingAs($user)->test(Settings::class);

        foreach (['one@example.com', 'two@example.com', 'three@example.com'] as $email) {
            $component
                ->set('newEmail', $email)
                ->set('emailChangePassword', 'password')
                ->call('requestEmailChange')
                ->assertHasNoErrors();
        }

        $component
            ->set('newEmail', 'four@example.com')
            ->set('emailChangePassword', 'password')
            ->call('requestEmailChange')
            ->assertHasErrors(['newEmail']);

        $this->assertDatabaseMissing('email_changes', ['new_email' => 'four@example.com']);
    }

    public function test_cancelling_the_form_clears_fields_and_validation_errors(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('newEmail', 'not-an-email')
            ->set('emailChangePassword', 'some-password')
            ->call('requestEmailChange')
            ->assertHasErrors(['newEmail'])
            ->call('resetEmailChangeForm')
            ->assertHasNoErrors()
            ->assertSet('newEmail', '')
            ->assertSet('emailChangePassword', '');
    }

    public function test_user_can_cancel_a_pending_email_change(): void
    {
        $user = User::factory()->create();

        EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('cancelEmailChange');

        $this->assertDatabaseCount('email_changes', 0);
    }

    public function test_user_can_resend_a_pending_confirmation_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('resendEmailChange');

        Notification::assertSentOnDemand(
            VerifyNewEmailAddress::class,
            fn (VerifyNewEmailAddress $notification, array $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === 'new@example.com'
        );
    }

    public function test_confirming_updates_email_and_syncs_stripe(): void
    {
        Notification::fake();
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_test123']);
        $oldEmail = $user->email;

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $oldEmail,
            'new_email' => 'new@example.com',
        ]);

        $response = $this->actingAs($user)->get($emailChange->confirmationUrl());

        $response->assertRedirect(route('customer.settings', ['tab' => 'account']));
        $response->assertSessionHas('email-changed');

        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($emailChange->fresh()->confirmed_at);

        Queue::assertPushed(SyncStripeCustomerDetailsJob::class, fn (SyncStripeCustomerDetailsJob $job) => $job->user->is($user));

        Notification::assertSentOnDemand(
            EmailChangeCompleted::class,
            fn (EmailChangeCompleted $notification, array $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === $oldEmail
        );
    }

    public function test_confirming_sets_email_verified_at_for_unverified_user(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        $this->actingAs($user)->get($emailChange->confirmationUrl());

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_confirmation_requires_authentication(): void
    {
        $user = User::factory()->create();

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        $this->get($emailChange->confirmationUrl())->assertRedirect();

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_confirmation_rejects_other_users(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        $this->actingAs($otherUser)->get($emailChange->confirmationUrl())->assertForbidden();

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_confirmation_rejects_unsigned_url(): void
    {
        $user = User::factory()->create();

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('email-change.confirm', ['emailChange' => $emailChange->id]))
            ->assertForbidden();

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_confirmation_rejects_expired_link(): void
    {
        $user = User::factory()->create();

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        $url = $emailChange->confirmationUrl();

        $this->travel(2)->hours();

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_confirmation_is_idempotent_once_confirmed(): void
    {
        Notification::fake();
        Queue::fake();

        $user = User::factory()->create();

        $emailChange = EmailChange::factory()->for($user)->confirmed()->create([
            'old_email' => 'previous@example.com',
            'new_email' => $user->email,
        ]);

        $this->actingAs($user)
            ->get($emailChange->confirmationUrl())
            ->assertRedirect(route('customer.settings', ['tab' => 'account']));

        Queue::assertNothingPushed();
        Notification::assertNothingSent();
    }

    public function test_confirmation_fails_when_email_taken_after_request(): void
    {
        Notification::fake();
        Queue::fake();

        $user = User::factory()->create();

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        User::factory()->create(['email' => 'new@example.com']);

        $response = $this->actingAs($user)->get($emailChange->confirmationUrl());

        $response->assertRedirect(route('customer.settings', ['tab' => 'account']));
        $response->assertSessionHas('email-change-failed');

        $this->assertSame($user->email, $user->fresh()->email);
        $this->assertNull($emailChange->fresh()->confirmed_at);
        Queue::assertNothingPushed();
    }

    public function test_confirming_updates_team_membership_emails(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $membership = TeamUser::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $conflictingMembership = TeamUser::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        TeamUser::factory()->create([
            'team_id' => $conflictingMembership->team_id,
            'email' => 'new@example.com',
        ]);

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $user->email,
            'new_email' => 'new@example.com',
        ]);

        $this->actingAs($user)->get($emailChange->confirmationUrl());

        $this->assertSame('new@example.com', $membership->fresh()->email);
        $this->assertSame($emailChange->old_email, $conflictingMembership->fresh()->email);
    }

    public function test_composer_credentials_cut_over_to_new_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(['plugin_license_key' => 'test-license-key-123']);
        $oldEmail = $user->email;

        $emailChange = EmailChange::factory()->for($user)->create([
            'old_email' => $oldEmail,
            'new_email' => 'new@example.com',
        ]);

        $this->actingAs($user)->get($emailChange->confirmationUrl());

        $this->withApiKey()
            ->asBasicAuth($oldEmail, 'test-license-key-123')
            ->getJson('/api/plugins/access')
            ->assertStatus(401);

        $this->withApiKey()
            ->asBasicAuth('new@example.com', 'test-license-key-123')
            ->getJson('/api/plugins/access')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => 'new@example.com',
                ],
            ]);
    }

    protected function asBasicAuth(string $username, string $password): static
    {
        return $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode("{$username}:{$password}"),
        ]);
    }

    protected function withApiKey(): static
    {
        return $this->withHeaders([
            'X-API-Key' => config('services.bifrost.api_key'),
        ]);
    }
}
