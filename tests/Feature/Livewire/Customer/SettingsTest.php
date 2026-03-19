<?php

namespace Tests\Feature\Livewire\Customer;

use App\Features\ShowAuthButtons;
use App\Livewire\Customer\Settings;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
    }

    // --- Page rendering ---

    public function test_settings_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/customer/settings');

        $response->assertStatus(200);
    }

    public function test_settings_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/customer/settings');

        $response->assertRedirect('/login');
    }

    public function test_settings_component_renders_headings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSee('Settings')
            ->assertSee('Name')
            ->assertSee('Password')
            ->assertSee('Delete Account')
            ->assertStatus(200);
    }

    // --- Update Name ---

    public function test_user_can_update_name(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSet('name', 'Old Name')
            ->set('name', 'New Name')
            ->call('updateName')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_name_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('name', '')
            ->call('updateName')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_name_has_max_length(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('name', str_repeat('a', 256))
            ->call('updateName')
            ->assertHasErrors(['name' => 'max']);
    }

    // --- Update Password ---

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'new-password-123')
            ->set('newPassword_confirmation', 'new-password-123')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSet('currentPassword', '')
            ->assertSet('newPassword', '')
            ->assertSet('newPassword_confirmation', '');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_wrong_current_password_fails(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('currentPassword', 'wrong-password')
            ->set('newPassword', 'new-password-123')
            ->set('newPassword_confirmation', 'new-password-123')
            ->call('updatePassword')
            ->assertHasErrors(['currentPassword']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('currentPassword', 'correct-password')
            ->set('newPassword', 'new-password-123')
            ->set('newPassword_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword']);
    }

    public function test_new_password_must_be_at_least_8_characters(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('currentPassword', 'correct-password')
            ->set('newPassword', 'short')
            ->set('newPassword_confirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword' => 'min']);
    }

    // --- Delete Account ---

    public function test_user_can_delete_account(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('my-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('deleteConfirmPassword', 'my-password')
            ->call('deleteAccount')
            ->assertHasNoErrors()
            ->assertRedirect(route('welcome'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_delete_account_with_wrong_password_keeps_user(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('my-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('deleteConfirmPassword', 'wrong-password')
            ->call('deleteAccount')
            ->assertHasErrors(['deleteConfirmPassword']);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_delete_account_removes_licenses(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('my-password'),
        ]);

        License::factory()->count(2)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('deleteConfirmPassword', 'my-password')
            ->call('deleteAccount')
            ->assertRedirect(route('welcome'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('licenses', ['user_id' => $user->id]);
    }

    // --- GitHub user password hint ---

    public function test_github_user_without_password_sees_hint(): void
    {
        $user = User::factory()->create(['github_id' => '12345']);

        // Bypass the hashed cast to set an empty password
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $user->id)
            ->update(['password' => '']);

        $user->refresh();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSee('Your account uses GitHub for authentication');
    }

    public function test_regular_user_does_not_see_github_hint(): void
    {
        $user = User::factory()->create([
            'github_id' => null,
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertDontSee('Your account uses GitHub for authentication');
    }
}
