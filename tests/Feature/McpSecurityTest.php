<?php

namespace Tests\Feature;

use Tests\TestCase;

class McpSecurityTest extends TestCase
{
    public function test_search_rejects_path_traversal_in_platform(): void
    {
        $response = $this->getJson('/api/mcp/search?q=test&platform=..');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['platform']);
    }

    public function test_search_rejects_path_traversal_in_version(): void
    {
        $response = $this->getJson('/api/mcp/search?q=test&version=..');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['version']);
    }

    public function test_search_rejects_excessive_limit(): void
    {
        $response = $this->getJson('/api/mcp/search?q=test&limit=1200');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['limit']);
    }

    public function test_search_accepts_valid_parameters(): void
    {
        $response = $this->getJson('/api/mcp/search?q=camera&platform=mobile&version=2&limit=10');

        $response->assertStatus(200);
        $response->assertJsonStructure(['results']);
    }

    public function test_search_allows_null_platform_and_version(): void
    {
        $response = $this->getJson('/api/mcp/search?q=camera');

        $response->assertStatus(200);
        $response->assertJsonStructure(['results']);
    }

    public function test_page_api_rejects_path_traversal(): void
    {
        $response = $this->getJson('/api/mcp/page/../../../etc/passwd');

        $response->assertStatus(404);
    }

    public function test_apis_endpoint_rejects_invalid_platform(): void
    {
        $response = $this->getJson('/api/mcp/apis/../1');

        $response->assertStatus(200);
        $response->assertJson(['apis' => []]);
    }

    public function test_navigation_endpoint_rejects_invalid_version(): void
    {
        $response = $this->getJson('/api/mcp/navigation/mobile/..');

        $response->assertStatus(200);
        $response->assertJson(['navigation' => []]);
    }
}
