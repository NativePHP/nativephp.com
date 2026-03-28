<?php

namespace Tests\Feature\Livewire\Customer;

use App\Features\ShowAuthButtons;
use App\Livewire\Customer\Notifications;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    // --- Page rendering ---

    public function test_notifications_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard/notifications');

        $response->assertStatus(200);
    }

    public function test_notifications_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/dashboard/notifications');

        $response->assertRedirect('/login');
    }

    public function test_notifications_component_renders_headings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertSee('Notifications')
            ->assertSee('Stay up to date with your account activity.')
            ->assertStatus(200);
    }

    // --- Displaying notifications ---

    public function test_notifications_display_in_reverse_chronological_order(): void
    {
        $user = User::factory()->create();

        $this->createNotification($user, ['title' => 'First'], now()->subHours(2));
        $this->createNotification($user, ['title' => 'Second'], now()->subHour());
        $this->createNotification($user, ['title' => 'Third'], now());

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertSeeInOrder(['Third', 'Second', 'First']);
    }

    public function test_empty_state_shown_when_no_notifications(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertSee('No notifications')
            ->assertSee("You're all caught up!", escape: false);
    }

    public function test_notification_title_and_body_are_displayed(): void
    {
        $user = User::factory()->create();

        $this->createNotification($user, [
            'title' => 'License Renewed',
            'body' => 'Your license has been renewed successfully.',
        ]);

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertSee('License Renewed')
            ->assertSee('Your license has been renewed successfully.');
    }

    // --- Mark as read ---

    public function test_mark_single_notification_as_read(): void
    {
        $user = User::factory()->create();

        $notification = $this->createNotification($user, ['title' => 'Test']);

        $this->assertNull($notification->read_at);

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->call('markAsRead', $notification->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_all_as_read(): void
    {
        $user = User::factory()->create();

        $n1 = $this->createNotification($user, ['title' => 'First']);
        $n2 = $this->createNotification($user, ['title' => 'Second']);

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->call('markAllAsRead');

        $this->assertNotNull($n1->fresh()->read_at);
        $this->assertNotNull($n2->fresh()->read_at);
    }

    public function test_mark_all_as_read_button_hidden_when_none_unread(): void
    {
        $user = User::factory()->create();

        $this->createNotification($user, ['title' => 'Read one'], now(), now());

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertDontSee('Mark all as read');
    }

    public function test_mark_all_as_read_button_shown_when_unread_exist(): void
    {
        $user = User::factory()->create();

        $this->createNotification($user, ['title' => 'Unread one']);

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertSee('Mark all as read');
    }

    // --- Settings link ---

    public function test_notifications_page_shows_link_to_notification_settings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->assertSee('Settings');
    }

    // --- Bell icon in layout ---

    public function test_bell_icon_shows_in_dashboard_layout(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard/settings');

        $response->assertStatus(200);
        $response->assertSee(route('customer.notifications'));
    }

    /**
     * Create a database notification for a user.
     */
    private function createNotification(User $user, array $data, ?Carbon $createdAt = null, ?Carbon $readAt = null): DatabaseNotification
    {
        return DatabaseNotification::create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\PluginApproved',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => $data,
            'read_at' => $readAt,
            'created_at' => $createdAt ?? now(),
            'updated_at' => $createdAt ?? now(),
        ]);
    }
}
