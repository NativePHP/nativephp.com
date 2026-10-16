<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class McpNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function initialized_notification_gets_202_with_no_body(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'method' => 'notifications/initialized',
        ]);

        $response->assertNoContent(202);
    }

    #[Test]
    public function any_notification_gets_202_with_no_body(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'method' => 'notifications/cancelled',
            'params' => ['requestId' => 1],
        ]);

        $response->assertNoContent(202);
    }

    #[Test]
    public function message_without_a_method_still_gets_an_error(): void
    {
        $response = $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
        ]);

        $response->assertOk();
        $response->assertJsonPath('error.code', -32000);
    }
}
