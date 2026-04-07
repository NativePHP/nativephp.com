<?php

namespace Tests\Feature\Actions\Licenses;

use App\Actions\Licenses\RotateLicenseKey;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RotateLicenseKeyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_rotates_a_license_key(): void
    {
        $newAnystackId = 'new-anystack-id';
        $newKey = 'new-license-key-uuid';

        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([
                'data' => [
                    'id' => $newAnystackId,
                    'key' => $newKey,
                    'expires_at' => now()->addYear()->toIso8601String(),
                    'created_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ],
            ], 201),
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([
                'data' => [
                    'suspended' => true,
                ],
            ], 200),
        ]);

        $user = User::factory()->create(['anystack_contact_id' => 'contact-123']);
        $license = License::factory()->active()->create([
            'user_id' => $user->id,
            'anystack_id' => 'old-anystack-id',
            'key' => 'old-license-key',
            'policy_name' => 'mini',
        ]);

        $action = resolve(RotateLicenseKey::class);
        $result = $action->handle($license);

        $license->refresh();
        $this->assertEquals($newAnystackId, $license->anystack_id);
        $this->assertEquals($newKey, $license->key);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                str_contains($request->url(), '/products/') &&
                str_contains($request->url(), '/licenses');
        });

        Http::assertSent(function ($request) {
            return $request->method() === 'PATCH' &&
                str_contains($request->url(), '/licenses/old-anystack-id') &&
                $request->data() === ['suspended' => true];
        });
    }

    #[Test]
    public function it_preserves_other_license_attributes_when_rotating(): void
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([
                'data' => [
                    'id' => 'new-anystack-id',
                    'key' => 'new-key',
                    'expires_at' => now()->addYear()->toIso8601String(),
                    'created_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ],
            ], 201),
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([], 200),
        ]);

        $user = User::factory()->create(['anystack_contact_id' => 'contact-123']);
        $license = License::factory()->active()->create([
            'user_id' => $user->id,
            'policy_name' => 'pro',
            'name' => 'My Production License',
        ]);

        $originalSubscriptionItemId = $license->subscription_item_id;

        $action = resolve(RotateLicenseKey::class);
        $action->handle($license);

        $license->refresh();
        $this->assertEquals($user->id, $license->user_id);
        $this->assertEquals('pro', $license->policy_name);
        $this->assertEquals('My Production License', $license->name);
        $this->assertEquals($originalSubscriptionItemId, $license->subscription_item_id);
        $this->assertFalse($license->is_suspended);
    }

    #[Test]
    public function it_fails_when_create_api_call_fails(): void
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([], 500),
        ]);

        $user = User::factory()->create(['anystack_contact_id' => 'contact-123']);
        $license = License::factory()->active()->create([
            'user_id' => $user->id,
            'anystack_id' => 'original-id',
            'key' => 'original-key',
            'policy_name' => 'mini',
        ]);

        $this->expectException(RequestException::class);

        $action = resolve(RotateLicenseKey::class);
        $action->handle($license);

        $license->refresh();
        $this->assertEquals('original-id', $license->anystack_id);
        $this->assertEquals('original-key', $license->key);
    }
}
