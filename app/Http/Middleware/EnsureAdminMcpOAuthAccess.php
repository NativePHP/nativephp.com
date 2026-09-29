<?php

namespace App\Http\Middleware;

use App\Enums\TokenAbility;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\AccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminMcpOAuthAccess
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();
        $token = $user?->currentAccessToken();

        abort_unless($token instanceof AccessToken, 401);
        abort_unless(
            $token->can(TokenAbility::AdminMcpServer->value) && $user->isAdmin(),
            403,
            'This connection cannot be used with the NativePHP admin MCP server. Only site admins with the mcp:admin scope may connect.'
        );

        return $next($request);
    }
}
