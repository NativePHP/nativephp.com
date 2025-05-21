<?php

namespace Tests\Feature\Services;

use App\Services\Anystack\Anystack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnystackTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_suspend_a_license()
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([
                'data' => [
                    'id' => 'license-123',
                    'suspended' => true,
                ],
            ], 200),
        ]);

        $anystack = new Anystack;
        $response = $anystack->suspendLicense('product-123', 'license-123');

        $this->assertEquals(200, $response->status());
        $this->assertEquals('license-123', $response->json('data.id'));
        $this->assertTrue($response->json('data.suspended'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/products/product-123/licenses/license-123') &&
                   $request->method() === 'PATCH' &&
                   $request->data() === ['suspended' => true];
        });
    }

    #[Test]
    public function it_throws_exception_when_api_call_fails()
    {
        Http::fake([
            'https://api.anystack.sh/v1/products/*/licenses/*' => Http::response([], 500),
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $anystack = new Anystack;
        $anystack->suspendLicense('product-123', 'license-123');
    }
}
