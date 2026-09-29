<?php

namespace App\Http\Middleware;

use App\Enums\TokenAbility;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddMcpOAuthChallenge
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() === 401) {
            $metadataPath = '/.well-known/oauth-protected-resource/mcp/oauth/admin';
            $scope = TokenAbility::AdminMcpServer->value;

            $response->headers->set('WWW-Authenticate', 'Bearer realm="mcp", resource_metadata="'
                .rtrim(config('app.url'), '/').$metadataPath.'", scope="'.$scope.'"');
        }

        return $response;
    }
}
