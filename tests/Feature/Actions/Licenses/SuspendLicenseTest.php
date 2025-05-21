<?php

namespace Tests\Feature\Actions\Licenses;

use App\Actions\Licenses\SuspendLicense;
use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuspendLicenseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_suspends_a_license()
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([
                'data' => [
                    'id' => 'license-123',
                    'suspended' => true,
                ],
            ], 200),
        ]);

        $license = License::factory()->create([
            'anystack_id' => 'license-123',
            'policy_name' => 'max',
            'is_suspended' => false,
        ]);

        $action = app(SuspendLicense::class);
        $result = $action->handle($license);

        $this->assertTrue($license->fresh()->is_suspended);

        Http::assertSent(function ($request) use ($license) {
            return str_contains($request->url(), '/products/') &&
                   str_contains($request->url(), "/licenses/{$license->anystack_id}") &&
                   $request->method() === 'PATCH' &&
                   $request->data() === ['suspended' => true];
        });
    }

    #[Test]
    public function it_fails_when_api_call_fails()
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([], 500),
        ]);

        $license = License::factory()->create([
            'anystack_id' => 'license-123',
            'policy_name' => 'max',
            'is_suspended' => false,
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $action = app(SuspendLicense::class);
        $result = $action->handle($license);

        $this->assertFalse($license->fresh()->is_suspended);
    }
}
