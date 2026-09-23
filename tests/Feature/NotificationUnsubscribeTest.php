<?php

namespace Tests\Feature;

use App\Http\Controllers\NotificationUnsubscribeController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationUnsubscribeTest extends TestCase
{
    use RefreshDatabase;

    // --- Unsubscribe ---

    public function test_unsubscribe_requires_valid_signature(): void
    {
        $user = User::factory()->create();

        $this->withoutVite()
            ->get(route('notifications.unsubscribe', ['user' => $user]))
            ->assertForbidden();
    }

    public function test_unauthenticated_user_can_unsubscribe_via_signed_url(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertViewIs('notifications.unsubscribed')
            ->assertSee('Notifications Disabled');

        $this->assertFalse($user->fresh()->receives_new_plugin_notifications);
    }

    public function test_unsubscribe_shows_masked_email_for_guest(): void
    {
        $user = User::factory()->create([
            'email' => 'janedoe@example.com',
            'receives_new_plugin_notifications' => true,
        ]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertSee('j*****e@example.com')
            ->assertDontSee('janedoe@example.com');
    }

    public function test_unsubscribe_shows_resubscribe_button_for_guest(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertSee('Re-enable Notifications');
    }

    public function test_authenticated_user_unsubscribe_redirects_to_settings(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->actingAs($user)
            ->get($url)
            ->assertRedirect(route('customer.settings', ['tab' => 'notifications']))
            ->assertSessionHas('new-plugin-notifications-disabled', true);

        $this->assertFalse($user->fresh()->receives_new_plugin_notifications);
    }

    public function test_unsubscribe_does_not_create_session_for_guest(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk();

        $this->assertGuest();
    }

    public function test_unsubscribe_is_idempotent(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => false]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk();

        $this->assertFalse($user->fresh()->receives_new_plugin_notifications);
    }

    // --- Resubscribe ---

    public function test_resubscribe_requires_valid_signature(): void
    {
        $user = User::factory()->create();

        $this->withoutVite()
            ->get(route('notifications.resubscribe', ['user' => $user]))
            ->assertForbidden();
    }

    public function test_unauthenticated_user_can_resubscribe_via_signed_url(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => false]);

        $url = url()->signedRoute('notifications.resubscribe', ['user' => $user]);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertViewIs('notifications.resubscribed')
            ->assertSee('Notifications Enabled');

        $this->assertTrue($user->fresh()->receives_new_plugin_notifications);
    }

    public function test_resubscribe_shows_masked_email_for_guest(): void
    {
        $user = User::factory()->create([
            'email' => 'janedoe@example.com',
            'receives_new_plugin_notifications' => false,
        ]);

        $url = url()->signedRoute('notifications.resubscribe', ['user' => $user]);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertSee('j*****e@example.com')
            ->assertDontSee('janedoe@example.com');
    }

    public function test_authenticated_user_resubscribe_redirects_to_settings(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => false]);

        $url = url()->signedRoute('notifications.resubscribe', ['user' => $user]);

        $this->withoutVite()
            ->actingAs($user)
            ->get($url)
            ->assertRedirect(route('customer.settings', ['tab' => 'notifications']))
            ->assertSessionHas('new-plugin-notifications-enabled', true);

        $this->assertTrue($user->fresh()->receives_new_plugin_notifications);
    }

    // --- Email masking edge cases ---

    public function test_short_email_local_part_is_masked_correctly(): void
    {
        $user = User::factory()->create([
            'email' => 'ab@example.com',
            'receives_new_plugin_notifications' => true,
        ]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertSee('a*@example.com')
            ->assertDontSee('ab@example.com');
    }

    public function test_single_char_email_local_part_is_masked_correctly(): void
    {
        $user = User::factory()->create([
            'email' => 'x@example.com',
            'receives_new_plugin_notifications' => true,
        ]);

        $url = NotificationUnsubscribeController::signedUnsubscribeUrl($user);

        $this->withoutVite()
            ->get($url)
            ->assertOk()
            ->assertDontSee('x@example.com');
    }
}
