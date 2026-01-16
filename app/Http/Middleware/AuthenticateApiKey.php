<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.bifrost.api_key');

        if (! $apiKey) {
            return response()->json(['message' => 'API key not configured'], 500);
        }

        // Prefer X-API-Key header (allows Basic Auth to coexist)
        // Fall back to Bearer token in Authorization header
        $providedKey = $request->header('X-API-Key');

        if (! $providedKey) {
            $authHeader = $request->header('Authorization');

            if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $providedKey = substr($authHeader, 7);
        }

        if (! hash_equals($apiKey, $providedKey)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
