<?php

namespace Tests\Feature\Api;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CreateLicenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Anystack API calls
        Http::fake([
            'https://api.anystack.sh/v1/contacts' => Http::response(['data' => ['id' => 'contact_123']], 200),
            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([
                'data' => [
                    'id' => 'license_123',
                    'key' => 'TEST-LICENSE-KEY',
                    'expires_at' => null,
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString(),
                ],
            ], 200),
        ]);
    }

    public function test_requires_authentication()
    {
        $response = $this->postJson('/api/licenses', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'subscription' => 'pro',
        ]);

        $response->assertStatus(401);
    }

    public function test_validates_required_fields()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'name', 'subscription']);
    }

    public function test_validates_subscription_enum()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'subscription' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subscription']);
    }

    public function test_creates_new_user_when_email_not_exists()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
            'subscription' => 'pro',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($newUser->password);
    }

    public function test_finds_existing_user_when_email_exists()
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Original Name',
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'existing@example.com',
            'name' => 'New Name',
            'subscription' => 'pro',
        ]);

        // User should not be updated, original name should remain
        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'name' => 'Original Name',
        ]);
    }

    public function test_creates_license_with_bifrost_source()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'subscription' => 'pro',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'policy_name',
                'source',
                'key',
                'created_at',
                'updated_at',
            ]);

        // Verify the license was created with correct attributes
        $this->assertDatabaseHas('licenses', [
            'policy_name' => 'pro',
            'source' => 'bifrost',
            'key' => 'TEST-LICENSE-KEY',
        ]);

        // Verify user was created/found
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_creates_license_for_existing_user()
    {
        // Create an existing user
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing User',
        ]);

        $authUser = User::factory()->create();
        $token = $authUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'existing@example.com',
            'name' => 'Different Name',  // This should be ignored
            'subscription' => 'max',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'policy_name',
                'source',
                'key',
            ]);

        // Verify license was created for the existing user
        $license = License::where('user_id', $existingUser->id)->first();
        $this->assertNotNull($license);
        $this->assertEquals('max', $license->policy_name);
        $this->assertEquals('bifrost', $license->source->value);
    }
}
