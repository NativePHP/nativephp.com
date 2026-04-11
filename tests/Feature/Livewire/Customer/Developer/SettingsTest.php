<?php

namespace Tests\Feature\Livewire\Customer\Developer;

use App\Features\AllowPaidPlugins;
use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Developer\Settings;
use App\Models\DeveloperAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
        Feature::define(AllowPaidPlugins::class, true);
    }

    // --- Page rendering ---

    public function test_developer_settings_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard/developer/settings');

        $response->assertStatus(200);
    }

    public function test_developer_settings_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/dashboard/developer/settings');

        $response->assertRedirect('/login');
    }

    public function test_developer_settings_component_renders_headings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSee('Developer Settings')
            ->assertSee('Author Display Name')
            ->assertStatus(200);
    }

    // --- Update Display Name ---

    public function test_user_can_update_display_name(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('displayName', 'My Dev Name')
            ->call('updateDisplayName')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'display_name' => 'My Dev Name',
        ]);
    }

    public function test_user_can_clear_display_name(): void
    {
        $user = User::factory()->create(['display_name' => 'Old Name']);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSet('displayName', 'Old Name')
            ->set('displayName', '')
            ->call('updateDisplayName')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'display_name' => null,
        ]);
    }

    public function test_display_name_has_max_length(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('displayName', str_repeat('a', 256))
            ->call('updateDisplayName')
            ->assertHasErrors(['displayName' => 'max']);
    }

    public function test_display_name_is_loaded_on_mount(): void
    {
        $user = User::factory()->create(['display_name' => 'Preset Name']);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSet('displayName', 'Preset Name');
    }

    // --- Stripe Connect Status ---

    public function test_active_developer_account_shows_status_badge(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSee('Stripe Account Status')
            ->assertSee('Account Active')
            ->assertSee('Your account is fully set up to receive payouts');
    }

    public function test_pending_developer_account_shows_setup_incomplete(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->pending()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSee('Stripe Account Status')
            ->assertSee('Setup Incomplete')
            ->assertSee('Continue Setup');
    }

    public function test_no_developer_account_shows_not_connected(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSee('Stripe Account Status')
            ->assertSee('Not Connected')
            ->assertSee('Connect Stripe');
    }

    public function test_stripe_section_hidden_when_paid_plugins_disabled(): void
    {
        Feature::define(AllowPaidPlugins::class, false);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertDontSee('Stripe Account Status');
    }
}
