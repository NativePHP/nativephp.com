<?php

namespace Tests\Feature\Api;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateLicenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
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

    public function test_dispatches_create_anystack_license_job()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'subscription' => 'pro',
        ]);

        Queue::assertPushed(CreateAnystackLicenseJob::class, function ($job) {
            return $job->subscription === Subscription::Pro
                && $job->firstName === null
                && $job->lastName === null
                && $job->source === LicenseSource::Bifrost
                && $job->subscriptionItemId === null;
        });
    }

    public function test_returns_existing_license_when_found()
    {
        $targetUser = User::factory()->create(['email' => 'test@example.com']);
        $license = License::factory()->create([
            'user_id' => $targetUser->id,
            'policy_name' => 'pro',
            'source' => LicenseSource::Bifrost,
        ]);

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
            ->assertJson([
                'id' => $license->id,
                'policy_name' => 'pro',
                'source' => 'bifrost',
            ]);
    }

    public function test_returns_pending_response_when_license_not_found()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/licenses', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
            'subscription' => 'mini',
        ]);

        $response->assertStatus(202)
            ->assertJson([
                'message' => 'License creation initiated. Please check back shortly.',
                'subscription' => 'mini',
            ]);
    }
}
