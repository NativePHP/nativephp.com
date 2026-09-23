<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_notification_toggles_are_visible_on_edit_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(EditUser::getUrl(['record' => $user]))
            ->assertSee('Notifications')
            ->assertSee('Email notifications')
            ->assertSee('New plugin notifications');
    }

    public function test_admin_can_disable_email_notifications(): void
    {
        $user = User::factory()->create(['receives_notification_emails' => true]);

        Livewire::actingAs($this->admin)
            ->test(EditUser::class, ['record' => $user->id])
            ->assertFormFieldExists('receives_notification_emails')
            ->fillForm(['receives_notification_emails' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($user->fresh()->receives_notification_emails);
    }

    public function test_admin_can_disable_new_plugin_notifications(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => true]);

        Livewire::actingAs($this->admin)
            ->test(EditUser::class, ['record' => $user->id])
            ->assertFormFieldExists('receives_new_plugin_notifications')
            ->fillForm(['receives_new_plugin_notifications' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($user->fresh()->receives_new_plugin_notifications);
    }

    public function test_admin_cannot_enable_user_disabled_email_notifications(): void
    {
        $user = User::factory()->create(['receives_notification_emails' => false]);

        Livewire::actingAs($this->admin)
            ->test(EditUser::class, ['record' => $user->id])
            ->assertFormFieldIsDisabled('receives_notification_emails');
    }

    public function test_admin_cannot_enable_user_disabled_new_plugin_notifications(): void
    {
        $user = User::factory()->create(['receives_new_plugin_notifications' => false]);

        Livewire::actingAs($this->admin)
            ->test(EditUser::class, ['record' => $user->id])
            ->assertFormFieldIsDisabled('receives_new_plugin_notifications');
    }

    public function test_notifications_section_shows_description(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(EditUser::getUrl(['record' => $user]))
            ->assertSee('Once these are disabled, they cannot be re-enabled by an admin.');
    }
}
