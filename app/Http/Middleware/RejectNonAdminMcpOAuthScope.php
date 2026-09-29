<?php

namespace App\Http\Middleware;

use App\Enums\TokenAbility;
use App\Support\OAuth\McpAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class RejectNonAdminMcpOAuthScope
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && $this->requestsAdminAccess($request) && ! $user->isAdmin()) {
            abort(403, 'Only NativePHP site admins may connect the admin MCP server.');
        }

        return $next($request);
    }

    private function requestsAdminAccess(Request $request): bool
    {
        if ($request->input('resource') === McpAccessToken::adminResource()) {
            return true;
        }

        $scope = $request->input('scope');

        if (is_string($scope) && $scope === TokenAbility::AdminMcpServer->value) {
            return true;
        }

        if (! $request->hasSession() || ! $request->session()->has('authRequest')) {
            return false;
        }

        $authRequest = unserialize($request->session()->get('authRequest'));

        if (! $authRequest instanceof AuthorizationRequestInterface) {
            return false;
        }

        foreach ($authRequest->getScopes() as $requestedScope) {
            if ($requestedScope->getIdentifier() === TokenAbility::AdminMcpServer->value) {
                return true;
            }
        }

        return false;
    }
}
