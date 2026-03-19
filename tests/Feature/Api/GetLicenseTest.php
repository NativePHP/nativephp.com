<?php

namespace Tests\Feature\Api;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLicenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication()
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'TEST-KEY-123',
        ]);

        $response = $this->getJson('/api/licenses/'.$license->key);

        $response->assertStatus(401);
    }

    public function test_returns_404_for_non_existent_license()
    {
        $token = config('services.bifrost.api_key');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/licenses/NON-EXISTENT-KEY');

        $response->assertStatus(404);
    }

    public function test_returns_license_with_user_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'TEST-LICENSE-KEY-123',
            'policy_name' => 'pro',
            'source' => 'bifrost',
            'anystack_id' => 'anystack_123',
        ]);

        $token = config('services.bifrost.api_key');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/licenses/'.$license->key);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $license->id,
                    'anystack_id' => 'anystack_123',
                    'key' => 'TEST-LICENSE-KEY-123',
                    'policy_name' => 'pro',
                    'source' => 'bifrost',
                    'email' => 'test@example.com',
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'anystack_id',
                    'key',
                    'policy_name',
                    'source',
                    'expires_at',
                    'created_at',
                    'updated_at',
                    'email',
                ],
            ]);
    }

    public function test_returns_correct_license_by_key()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $license1 = License::factory()->create([
            'user_id' => $user1->id,
            'key' => 'KEY-USER-1',
        ]);

        $license2 = License::factory()->create([
            'user_id' => $user2->id,
            'key' => 'KEY-USER-2',
        ]);

        $token = config('services.bifrost.api_key');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/licenses/KEY-USER-2');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $license2->id,
                    'key' => 'KEY-USER-2',
                    'email' => 'user2@example.com',
                ],
            ]);
    }
}
