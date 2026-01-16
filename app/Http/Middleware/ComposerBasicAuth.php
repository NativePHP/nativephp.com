<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ComposerBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * Validates HTTP Basic Auth credentials for Composer clients.
     * Expects email as username and plugin_license_key as password.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->getUser();
        $licenseKey = $request->getPassword();

        if (! $email || ! $licenseKey) {
            return $this->unauthorized('Authentication required');
        }

        $user = User::where('email', $email)
            ->where('plugin_license_key', $licenseKey)
            ->first();

        if (! $user) {
            Log::info('Plugin repository auth failed', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->unauthorized('Invalid credentials');
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    protected function unauthorized(string $message): Response
    {
        return response($message, 401, [
            'WWW-Authenticate' => 'Basic realm="NativePHP Plugin Repository"',
        ]);
    }
}
