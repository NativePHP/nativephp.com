<?php

namespace App\Http\Controllers\OAuth;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Support\OAuth\McpAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;

class McpOAuthController extends Controller
{
    public function adminProtectedResource(): JsonResponse
    {
        return response()->json([
            'resource' => McpAccessToken::adminResource(),
            'authorization_servers' => [rtrim(config('app.url'), '/')],
            'scopes_supported' => [TokenAbility::AdminMcpServer->value],
            'bearer_methods_supported' => ['header'],
        ]);
    }

    public function authorizationServer(): JsonResponse
    {
        $issuer = rtrim(config('app.url'), '/');

        return response()->json([
            'issuer' => $issuer,
            'authorization_endpoint' => $issuer.'/oauth/authorize',
            'token_endpoint' => $issuer.'/oauth/token',
            'registration_endpoint' => $issuer.'/oauth/register',
            'response_types_supported' => ['code'],
            'code_challenge_methods_supported' => ['S256'],
            'scopes_supported' => [
                TokenAbility::AdminMcpServer->value,
            ],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'token_endpoint_auth_methods_supported' => ['none'],
        ]);
    }

    public function revoke(Request $request, string $token): Response
    {
        $tokens = Passport::token()->newQuery()->where('user_id', $request->user()->getAuthIdentifier());
        $connection = (clone $tokens)->findOrFail($token);

        Passport::authCode()->newQuery()
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('client_id', $connection->client_id)
            ->update(['revoked' => true]);

        $tokens->where('client_id', $connection->client_id)->each(function ($accessToken): void {
            $accessToken->refreshToken()->update(['revoked' => true]);
            $accessToken->revoke();
        });

        return response()->noContent();
    }
}
