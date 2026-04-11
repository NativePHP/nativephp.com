<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Livewire\Customer\Licenses\Show;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerLicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable the auth feature flag for all license management tests
        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_customer_can_view_licenses_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/licenses');

        $response->assertStatus(200);
        $response->assertSee('Your Licenses');
        $response->assertSee('Manage your NativePHP licenses');
    }

    public function test_customer_sees_no_licenses_message_when_no_licenses_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/licenses');

        $response->assertStatus(200);
        $response->assertSee('No licenses found');
        $response->assertSee('believe this is an error');
    }

    public function test_customer_can_view_their_licenses(): void
    {
        $user = User::factory()->create();
        $license1 = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'Standard License',
            'key' => 'test-key-1',
        ]);
        $license2 = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'Premium License',
            'key' => 'test-key-2',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses');

        $response->assertStatus(200);
        $response->assertSee('Standard License');
        $response->assertSee('Premium License');
        $response->assertSee('test-key-1');
        $response->assertSee('test-key-2');
    }

    public function test_customer_cannot_view_other_customers_licenses(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $license1 = License::factory()->create([
            'user_id' => $user1->id,
            'policy_name' => 'User 1 License',
        ]);
        $license2 = License::factory()->create([
            'user_id' => $user2->id,
            'policy_name' => 'User 2 License',
        ]);

        $response = $this->actingAs($user1)->get('/dashboard/licenses');

        $response->assertStatus(200);
        $response->assertSee('User 1 License');
        $response->assertDontSee('User 2 License');
    }

    public function test_customer_can_view_individual_license_details(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'key' => 'test-license-key-123',
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses/'.$license->key);

        $response->assertStatus(200);
        $response->assertSee('pro');
        $response->assertSee('test-license-key-123');
        $response->assertSee('License Information');
        $response->assertSee('Active');
    }

    public function test_customer_cannot_view_other_customers_license_details(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $license = License::factory()->create([
            'user_id' => $user2->id,
            'key' => 'other-user-license',
        ]);

        $response = $this->actingAs($user1)->get('/dashboard/licenses/'.$license->key);

        $response->assertStatus(404);
    }

    public function test_license_status_displays_correctly(): void
    {
        $user = User::factory()->create();

        // Active license
        $activeLicense = License::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30),
            'is_suspended' => false,
        ]);

        // Expired license
        $expiredLicense = License::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->subDays(1),
            'is_suspended' => false,
        ]);

        // Suspended license
        $suspendedLicense = License::factory()->create([
            'user_id' => $user->id,
            'is_suspended' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses');

        $response->assertStatus(200);
        $response->assertSee('Active');
        $response->assertSee('Expired');
        $response->assertSee('Suspended');
    }

    public function test_customer_can_update_license_name(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'test-license-key',
            'name' => null,
        ]);

        $response = $this->actingAs($user)
            ->patch('/dashboard/licenses/'.$license->key, [
                'name' => 'My Production License',
            ]);

        $response->assertRedirect('/dashboard/licenses/'.$license->key);
        $response->assertSessionHas('success', 'License name updated successfully!');

        $this->assertDatabaseHas('licenses', [
            'id' => $license->id,
            'name' => 'My Production License',
        ]);
    }

    public function test_customer_can_clear_license_name(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'test-license-key',
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($user)
            ->patch('/dashboard/licenses/'.$license->key, [
                'name' => '',
            ]);

        $response->assertRedirect('/dashboard/licenses/'.$license->key);
        $response->assertSessionHas('success', 'License name updated successfully!');

        $this->assertDatabaseHas('licenses', [
            'id' => $license->id,
            'name' => null,
        ]);
    }

    public function test_license_name_validation(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'test-license-key',
        ]);

        $response = $this->actingAs($user)
            ->patch('/dashboard/licenses/'.$license->key, [
                'name' => str_repeat('a', 256), // Too long
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_customer_cannot_update_other_customers_license_name(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $license = License::factory()->create([
            'user_id' => $user2->id,
            'key' => 'other-user-license',
        ]);

        $response = $this->actingAs($user1)
            ->patch('/dashboard/licenses/'.$license->key, [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(404);
    }

    public function test_license_names_display_on_list_page(): void
    {
        $user = User::factory()->create();

        $namedLicense = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'name' => 'My Custom License Name',
        ]);

        $unnamedLicense = License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => 'starter',
            'name' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses');

        $response->assertStatus(200);
        // Named license should show custom name prominently
        $response->assertSee('My Custom License Name');
        $response->assertSee('pro');
        // Unnamed license should show policy name
        $response->assertSee('starter');
    }

    public function test_license_name_displays_on_show_page(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'test-license-key',
            'name' => 'My Custom License',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses/'.$license->key);

        $response->assertStatus(200);
        $response->assertSee('My Custom License');
        $response->assertSee('License Name');
    }

    public function test_license_show_page_displays_no_name_set_when_name_is_null(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'test-license-key',
            'name' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses/'.$license->key);

        $response->assertStatus(200);
        $response->assertSee('No name set');
    }

    public function test_dashboard_shows_license_count(): void
    {
        $user = User::factory()->create();
        License::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('View licenses');
    }

    public function test_dashboard_hides_licenses_card_when_user_has_no_licenses(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('View licenses');
    }

    public function test_customer_can_rotate_license_key(): void
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([
                'data' => [
                    'id' => 'new-anystack-id',
                    'key' => 'new-rotated-key',
                    'expires_at' => now()->addYear()->toIso8601String(),
                    'created_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ],
            ], 201),
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([
                'data' => ['suspended' => true],
            ], 200),
        ]);

        $user = User::factory()->create(['anystack_contact_id' => 'contact-123']);
        $license = License::factory()->active()->create([
            'user_id' => $user->id,
            'policy_name' => 'mini',
            'key' => 'old-key-to-rotate',
            'anystack_id' => 'old-anystack-id',
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['licenseKey' => 'old-key-to-rotate'])
            ->call('rotateLicenseKey')
            ->assertRedirect(route('customer.licenses.show', 'new-rotated-key'));

        $license->refresh();
        $this->assertEquals('new-rotated-key', $license->key);
        $this->assertEquals('new-anystack-id', $license->anystack_id);
        $this->assertFalse($license->is_suspended);
    }

    public function test_customer_cannot_rotate_suspended_license_key(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->suspended()->create([
            'user_id' => $user->id,
            'key' => 'suspended-license-key',
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['licenseKey' => 'suspended-license-key'])
            ->call('rotateLicenseKey')
            ->assertNoRedirect();

        Http::assertNothingSent();
    }

    public function test_customer_cannot_rotate_expired_license_key(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->expired()->create([
            'user_id' => $user->id,
            'key' => 'expired-license-key',
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['licenseKey' => 'expired-license-key'])
            ->call('rotateLicenseKey')
            ->assertNoRedirect();

        Http::assertNothingSent();
    }

    public function test_active_license_shows_rotate_button(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->active()->create([
            'user_id' => $user->id,
            'key' => 'active-license-key',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses/active-license-key');

        $response->assertStatus(200);
        $response->assertSee('Rotate key');
    }

    public function test_suspended_license_does_not_show_rotate_button(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->suspended()->create([
            'user_id' => $user->id,
            'key' => 'suspended-license-key',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/licenses/suspended-license-key');

        $response->assertStatus(200);
        $response->assertDontSee('Rotate key');
    }
}
