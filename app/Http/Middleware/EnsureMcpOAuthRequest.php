<?php

namespace App\Http\Middleware;

use App\Support\OAuth\McpAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMcpOAuthRequest
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $resource = $request->input('resource');
        $expectedScope = is_string($resource) ? McpAccessToken::scopeForResource($resource) : null;

        if ($expectedScope === null) {
            return response()->json([
                'error' => 'invalid_target',
                'error_description' => 'Specify the exact NativePHP admin OAuth MCP resource URL.',
            ], 400);
        }

        if ($request->isMethod('get')
            && ($request->input('code_challenge_method') !== 'S256'
                || ! is_string($request->input('code_challenge'))
                || ! preg_match('/^[A-Za-z0-9_-]{43}$/D', $request->input('code_challenge')))) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => 'An S256 PKCE code challenge is required.',
            ], 400);
        }

        if ($request->isMethod('post')
            && ! in_array($request->input('grant_type'), ['authorization_code', 'refresh_token'], true)) {
            return response()->json(['error' => 'unsupported_grant_type'], 400);
        }

        if ($request->isMethod('get') && ! $request->has('scope')) {
            $request->merge(['scope' => $expectedScope]);
        }

        if ($request->has('scope') && $request->input('scope') !== $expectedScope) {
            return response()->json(['error' => 'invalid_scope'], 400);
        }

        return $next($request);
    }
}
