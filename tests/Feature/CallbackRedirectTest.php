<?php

namespace Tests\Feature;

use Tests\TestCase;

class CallbackRedirectTest extends TestCase
{
    public function test_callback_redirects_to_nativephp_url_with_token(): void
    {
        $response = $this->get('/callback?url=nativephp://127.0.0.1/some/url');

        $response->assertRedirect();

        $redirectUrl = $response->headers->get('Location');

        $this->assertStringStartsWith('nativephp://127.0.0.1/some/url?token=', $redirectUrl);

        // Extract and validate the token is a valid UUID
        $token = str_replace('nativephp://127.0.0.1/some/url?token=', '', $redirectUrl);
        $this->assertTrue(
            preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $token) === 1,
            "Token should be a valid UUID, got: {$token}"
        );
    }

    public function test_callback_shows_goodbye_for_non_nativephp_url(): void
    {
        $response = $this->get('/callback?url=https://example.com');

        $response->assertStatus(200);
        $response->assertSee('Goodbye');
    }

    public function test_callback_shows_goodbye_when_no_url_provided(): void
    {
        $response = $this->get('/callback');

        $response->assertStatus(200);
        $response->assertSee('Goodbye');
    }

    public function test_callback_shows_goodbye_for_partial_nativephp_scheme(): void
    {
        $response = $this->get('/callback?url=nativephp:/missing-slash');

        $response->assertStatus(200);
        $response->assertSee('Goodbye');
    }
}
