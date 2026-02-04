<?php

namespace Tests\Feature\Api;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LicenseRenewalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Anystack API renew endpoint
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses/*/renew' => Http::response([
                'data' => [
                    'id' => 'license_123',
                    'key' => 'TEST-LICENSE-KEY',
                    'expires_at' => now()->addYear()->toISOString(),
                    'updated_at' => now()->toISOString(),
                ],
            ], 200),
        ]);
    }

    public function test_requires_authentication(): void
    {
        $user = User::factory()->create();
        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'TEST-KEY-123',
        ]);

        $response = $this->patchJson('/api/licenses/'.$license->key.'/renew');

        $response->assertStatus(401);
    }

    public function test_returns_404_for_non_existent_license(): void
    {
        $token = config('services.bifrost.api_key');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->patchJson('/api/licenses/NON-EXISTENT-KEY/renew');

        $response->assertStatus(404);
    }

    public function test_successfully_renews_license_and_returns_new_expiry_date(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $currentExpiry = now()->addMonths(3);

        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'TEST-LICENSE-KEY',
            'policy_name' => 'pro',
            'source' => 'bifrost',
            'anystack_id' => 'license_123',
            'expires_at' => $currentExpiry,
        ]);

        $token = config('services.bifrost.api_key');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->patchJson('/api/licenses/'.$license->key.'/renew');

        $response->assertStatus(200)
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
            ])
            ->assertJson([
                'data' => [
                    'id' => $license->id,
                    'anystack_id' => 'license_123',
                    'key' => 'TEST-LICENSE-KEY',
                    'policy_name' => 'pro',
                    'source' => 'bifrost',
                    'email' => 'test@example.com',
                ],
            ]);

        // Verify the expiry date was updated in the database
        $license->refresh();
        $this->assertNotNull($license->expires_at);
        $this->assertTrue(
            $license->expires_at->greaterThan($currentExpiry),
            'New expiry date should be after the current expiry date'
        );

        // Verify Anystack API was called
        Http::assertSent(function ($request) use ($license) {
            return $request->url() === "https://api.anystack.sh/v1/products/{$license->anystack_product_id}/licenses/{$license->anystack_id}/renew"
                && $request->method() === 'PATCH';
        });
    }

    public function test_renews_license_without_anystack_id_logs_error(): void
    {
        $user = User::factory()->create();

        $license = License::factory()->create([
            'user_id' => $user->id,
            'key' => 'TEST-LICENSE-KEY',
            'anystack_id' => null, // No Anystack ID
            'expires_at' => now()->addMonths(3),
        ]);

        $token = config('services.bifrost.api_key');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->patchJson('/api/licenses/'.$license->key.'/renew');

        // Should still return 200 but the expiry won't be updated
        $response->assertStatus(200);

        // Verify the expiry date was NOT updated
        $license->refresh();
        $this->assertEquals(
            now()->addMonths(3)->format('Y-m-d H:i'),
            $license->expires_at->format('Y-m-d H:i'),
            'Expiry date should remain unchanged when no anystack_id'
        );

        // Verify Anystack API was NOT called
        Http::assertNothingSent();
    }
}
