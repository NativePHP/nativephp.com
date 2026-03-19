<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Livewire\SubLicenseManager;
use App\Models\License;
use App\Models\SubLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerSubLicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable the auth feature flag for all sub-license management tests
        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_customer_can_create_sub_license_when_license_supports_it(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'anystack_contact_id' => fake()->uuid(),
        ]);
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro', // Pro supports sub-licenses with limit of 10
            'is_suspended' => false,
            'expires_at' => now()->addDays(30),
            'anystack_id' => fake()->uuid(),
        ]);

        $response = $this->actingAs($user)
            ->post("/customer/licenses/{$license->key}/sub-licenses", [
                'name' => 'Development Team',
            ]);

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHas('success', 'Sub-license is being created. You will receive an email notification when it\'s ready.');

        Queue::assertPushed(\App\Jobs\CreateAnystackSubLicenseJob::class);
    }

    public function test_customer_can_create_sub_license_without_name(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'anystack_contact_id' => fake()->uuid(),
        ]);
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'is_suspended' => false,
            'expires_at' => now()->addDays(30),
            'anystack_id' => fake()->uuid(),
        ]);

        $response = $this->actingAs($user)
            ->post("/customer/licenses/{$license->key}/sub-licenses", [
                'name' => '',
            ]);

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHas('success', 'Sub-license is being created. You will receive an email notification when it\'s ready.');

        Queue::assertPushed(\App\Jobs\CreateAnystackSubLicenseJob::class);
    }

    public function test_customer_cannot_create_sub_license_for_suspended_license(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'is_suspended' => true,
        ]);

        $response = $this->actingAs($user)
            ->post("/customer/licenses/{$license->key}/sub-licenses", [
                'name' => 'Development Team',
            ]);

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHasErrors(['sub_license']);

        $this->assertDatabaseMissing('sub_licenses', [
            'parent_license_id' => $license->id,
        ]);
    }

    public function test_customer_cannot_create_sub_license_for_expired_license(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'is_suspended' => false,
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($user)
            ->post("/customer/licenses/{$license->key}/sub-licenses", [
                'name' => 'Development Team',
            ]);

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHasErrors(['sub_license']);

        $this->assertDatabaseMissing('sub_licenses', [
            'parent_license_id' => $license->id,
        ]);
    }

    public function test_customer_can_update_sub_license_name(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);
        $subLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($user)
            ->patch("/customer/licenses/{$license->key}/sub-licenses/{$subLicense->id}", [
                'name' => 'New Name',
            ]);

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHas('success', 'Sub-license updated successfully!');

        $this->assertDatabaseHas('sub_licenses', [
            'id' => $subLicense->id,
            'name' => 'New Name',
        ]);
    }

    public function test_customer_can_suspend_sub_license(): void
    {
        Http::fake([
            'api.anystack.sh/v1/products/*/licenses/*' => Http::response(['success' => true], 200),
        ]);

        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);
        $subLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($user)
            ->patch("/customer/licenses/{$license->key}/sub-licenses/{$subLicense->id}/suspend");

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHas('success', 'Sub-license suspended successfully!');

        $this->assertDatabaseHas('sub_licenses', [
            'id' => $subLicense->id,
            'is_suspended' => true,
        ]);
    }

    public function test_customer_can_delete_sub_license(): void
    {
        Http::fake([
            'api.anystack.sh/v1/products/*/licenses/*' => Http::response(['success' => true], 200),
        ]);

        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);
        $subLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/customer/licenses/{$license->key}/sub-licenses/{$subLicense->id}");

        $response->assertRedirect("/customer/licenses/{$license->key}")
            ->assertSessionHas('success', 'Sub-license deleted successfully!');

        $this->assertDatabaseMissing('sub_licenses', [
            'id' => $subLicense->id,
        ]);
    }

    public function test_customer_cannot_access_other_customer_sub_licenses(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $license1 = License::factory()->create(['user_id' => $user1->id]);
        $license2 = License::factory()->create(['user_id' => $user2->id]);

        $subLicense = SubLicense::factory()->create([
            'parent_license_id' => $license2->id,
        ]);

        // Try to update another user's sub-license
        $response = $this->actingAs($user1)
            ->patch("/customer/licenses/{$license2->key}/sub-licenses/{$subLicense->id}", [
                'name' => 'Malicious Update',
            ]);

        $response->assertStatus(404);

        // Try to delete another user's sub-license
        $response = $this->actingAs($user1)
            ->delete("/customer/licenses/{$license2->key}/sub-licenses/{$subLicense->id}");

        $response->assertStatus(404);
    }

    public function test_customer_cannot_manage_sub_license_with_wrong_parent_license(): void
    {
        $user = User::factory()->create();
        $license1 = License::factory()->create(['user_id' => $user->id]);
        $license2 = License::factory()->create(['user_id' => $user->id]);

        $subLicense = SubLicense::factory()->create([
            'parent_license_id' => $license2->id,
        ]);

        // Try to manage sub-license using wrong parent license key
        $response = $this->actingAs($user)
            ->patch("/customer/licenses/{$license1->key}/sub-licenses/{$subLicense->id}", [
                'name' => 'Wrong Parent',
            ]);

        $response->assertStatus(404);
    }

    public function test_sub_license_inherits_expiry_from_parent_license(): void
    {
        $user = User::factory()->create();
        $expiresAt = now()->addDays(30);
        $license = License::factory()->create([
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
        ]);

        // Test the model boot logic directly by creating a sub-license
        $subLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'expires_at' => null, // Let the boot method set it
        ]);

        $this->assertEquals($expiresAt->toDateString(), $subLicense->expires_at->toDateString());
    }

    public function test_sub_license_shows_correct_status(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create(['user_id' => $user->id]);

        // Test active status
        $activeSubLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'is_suspended' => false,
            'expires_at' => now()->addDays(30),
        ]);

        $this->assertEquals('Active', $activeSubLicense->status);
        $this->assertTrue($activeSubLicense->isActive());
        $this->assertFalse($activeSubLicense->isExpired());

        // Test suspended status
        $suspendedSubLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'is_suspended' => true,
            'expires_at' => now()->addDays(30),
        ]);

        $this->assertEquals('Suspended', $suspendedSubLicense->status);
        $this->assertFalse($suspendedSubLicense->isActive());

        // Test expired status
        $expiredSubLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'is_suspended' => false,
            'expires_at' => now()->subDays(1),
        ]);

        $this->assertEquals('Expired', $expiredSubLicense->status);
        $this->assertFalse($expiredSubLicense->isActive());
        $this->assertTrue($expiredSubLicense->isExpired());
    }

    public function test_license_show_page_displays_sub_licenses(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);

        $subLicense1 = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'name' => 'Development Team',
            'is_suspended' => false,
        ]);

        $subLicense2 = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'name' => 'Testing Team',
            'is_suspended' => true,
        ]);

        $response = $this->actingAs($user)->get("/customer/licenses/{$license->key}");

        $response->assertStatus(200);
        $response->assertSee('Keys');
        $response->assertSee('Development Team');
        $response->assertSee('Testing Team');
        $response->assertSee($subLicense1->key);
        $response->assertSee($subLicense2->key);
        $response->assertSee('Active');
        $response->assertSee('Suspended');
    }

    public function test_validation_for_sub_license_name(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);

        // Test name too long
        $response = $this->actingAs($user)
            ->post("/customer/licenses/{$license->key}/sub-licenses", [
                'name' => str_repeat('a', 256), // 256 characters, should fail
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_livewire_component_starts_polling_when_create_key_button_clicked(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);

        $this->actingAs($user);

        Livewire::test(SubLicenseManager::class, ['license' => $license])
            ->assertSet('isPolling', false)
            ->call('startPolling')
            ->assertSet('isPolling', true);
    }

    public function test_livewire_component_stops_polling_when_new_sublicense_appears(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(SubLicenseManager::class, ['license' => $license])
            ->assertSet('isPolling', false)
            ->assertSet('initialSubLicenseCount', 0)
            ->call('startPolling')
            ->assertSet('isPolling', true);

        // Create a new sublicense
        SubLicense::factory()->create([
            'parent_license_id' => $license->id,
        ]);

        // Re-render the component (simulating a poll)
        $component->call('$refresh')
            ->assertSet('isPolling', false)
            ->assertSet('initialSubLicenseCount', 1);
    }

    public function test_livewire_component_displays_sublicenses(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
        ]);

        $activeSubLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'name' => 'Active Key',
            'is_suspended' => false,
        ]);

        $suspendedSubLicense = SubLicense::factory()->create([
            'parent_license_id' => $license->id,
            'name' => 'Suspended Key',
            'is_suspended' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(SubLicenseManager::class, ['license' => $license])
            ->assertSee('Active Key')
            ->assertSee('Suspended Key')
            ->assertSee($activeSubLicense->key)
            ->assertSee($suspendedSubLicense->key);
    }
}
