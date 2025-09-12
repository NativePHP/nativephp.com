<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerLicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_licenses_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/customer/licenses');

        $response->assertStatus(200);
        $response->assertSee('Your Licenses');
        $response->assertSee('Manage your NativePHP licenses');
    }

    public function test_customer_sees_no_licenses_message_when_no_licenses_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/customer/licenses');

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

        $response = $this->actingAs($user)->get('/customer/licenses');

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

        $response = $this->actingAs($user1)->get('/customer/licenses');

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

        $response = $this->actingAs($user)->get('/customer/licenses/'.$license->key);

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

        $response = $this->actingAs($user1)->get('/customer/licenses/'.$license->key);

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

        $response = $this->actingAs($user)->get('/customer/licenses');

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
            ->patch('/customer/licenses/'.$license->key, [
                'name' => 'My Production License',
            ]);

        $response->assertRedirect('/customer/licenses/'.$license->key);
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
            ->patch('/customer/licenses/'.$license->key, [
                'name' => '',
            ]);

        $response->assertRedirect('/customer/licenses/'.$license->key);
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
            ->patch('/customer/licenses/'.$license->key, [
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
            ->patch('/customer/licenses/'.$license->key, [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(404);
    }

    public function test_license_names_display_on_index_page(): void
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

        $response = $this->actingAs($user)->get('/customer/licenses');

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

        $response = $this->actingAs($user)->get('/customer/licenses/'.$license->key);

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

        $response = $this->actingAs($user)->get('/customer/licenses/'.$license->key);

        $response->assertStatus(200);
        $response->assertSee('No name set');
    }
}
