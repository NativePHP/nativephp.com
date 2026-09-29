<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            Integration::captureUnhandledException($e);
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * Passport's AuthorizationController throws AuthenticationException for guests.
     * Laravel's default falls back to route('login'), which this app does not define
     * at /login (customer login is customer.login; admin is filament.admin.auth.login).
     *
     * @param  Request  $request
     * @return Response|JsonResponse|RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->shouldReturnJson($request, $exception)) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        if ($this->isOAuthAuthenticationRequest($request)) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        return redirect()->guest($exception->redirectTo($request) ?? route('customer.login'));
    }

    private function isOAuthAuthenticationRequest(Request $request): bool
    {
        return $request->is('oauth/*') || $request->routeIs('passport.*');
    }
}
