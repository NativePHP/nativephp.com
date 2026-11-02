<?php

namespace Tests\Concerns;

use App\Models\User;
use App\Support\OAuth\McpAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

trait InteractsWithMcpOAuth
{
    protected function configureMcpOAuthKeys(): void
    {
        static $keys;

        if ($keys === null) {
            $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
            openssl_pkey_export($key, $privateKey);
            $keys = [$privateKey, openssl_pkey_get_details($key)['key']];
        }

        config([
            'passport.private_key' => $keys[0],
            'passport.public_key' => $keys[1],
        ]);

        $this->withoutVite();
    }

    protected function mcpAdminOAuthResource(): string
    {
        return McpAccessToken::adminResource();
    }

    protected function createMcpOAuthClient(): Client
    {
        return app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            name: 'Example Assistant',
            redirectUris: ['https://assistant.example/callback'],
            confidential: false,
        );
    }

    /**
     * @return array<string, string>
     */
    protected function mcpOAuthAuthorizationParameters(
        Client $client,
        string $verifier,
        string $scope = 'mcp:admin',
        ?string $resource = null,
    ): array {
        return [
            'client_id' => (string) $client->id,
            'redirect_uri' => $client->redirect_uris[0],
            'response_type' => 'code',
            'scope' => $scope,
            'state' => Str::random(40),
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
            'code_challenge_method' => 'S256',
            'resource' => $resource ?? $this->mcpAdminOAuthResource(),
            'prompt' => 'consent',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function approveMcpOAuthAuthorization(
        User $user,
        ?Client $client = null,
        string $scope = 'mcp:admin',
        ?string $resource = null,
    ): array {
        $client ??= $this->createMcpOAuthClient();
        $verifier = Str::random(64);
        $resource ??= $this->mcpAdminOAuthResource();
        $parameters = $this->mcpOAuthAuthorizationParameters($client, $verifier, $scope, $resource);

        $this->flushHeaders();
        Auth::forgetGuards();
        $this->actingAs($user, 'web')
            ->get('/oauth/authorize?'.http_build_query($parameters))
            ->assertOk()
            ->assertSee('Example Assistant');

        $response = $this->post('/oauth/authorize', [
            'auth_token' => session('authToken'),
            'client_id' => (string) $client->id,
        ])->assertRedirect();

        $this->assertStringStartsWith($client->redirect_uris[0].'?', $response->headers->get('Location'));
        parse_str(parse_url($response->headers->get('Location'), PHP_URL_QUERY), $query);
        $this->assertArrayHasKey('code', $query);
        $this->assertSame($parameters['state'], $query['state']);

        return [
            'grant_type' => 'authorization_code',
            'client_id' => (string) $client->id,
            'redirect_uri' => $client->redirect_uris[0],
            'code' => $query['code'],
            'code_verifier' => $verifier,
            'resource' => $resource,
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int, token_type: string}
     */
    protected function issueMcpOAuthTokens(
        User $user,
        ?Client $client = null,
        string $scope = 'mcp:admin',
        ?string $resource = null,
    ): array {
        return $this->post('/oauth/token', $this->approveMcpOAuthAuthorization($user, $client, $scope, $resource), ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonStructure(['access_token', 'refresh_token', 'expires_in', 'token_type'])
            ->json();
    }

    protected function resetMcpAuthentication(): void
    {
        $this->flushHeaders();
        $this->flushSession();
        Auth::forgetGuards();
        Auth::shouldUse('web');
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function callMcpWithBearer(string $token, string $method = 'initialize', array $params = [], string $endpoint = '/mcp/oauth/admin'): TestResponse
    {
        $this->resetMcpAuthentication();

        if ($method === 'initialize') {
            $params = [
                'protocolVersion' => '2025-03-26',
                'capabilities' => (object) [],
                'clientInfo' => ['name' => 'phpunit-oauth', 'version' => '1.0.0'],
            ];
        }

        return $this->withToken($token)->postJson($endpoint, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => (object) $params,
        ], ['Accept' => 'application/json, text/event-stream']);
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function callMcpTool(string $token, string $name, array $arguments = []): TestResponse
    {
        return $this->callMcpWithBearer($token, 'tools/call', [
            'name' => $name,
            'arguments' => (object) $arguments,
        ]);
    }
}
