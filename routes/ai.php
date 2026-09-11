<?php

use App\Http\Controllers\OAuth\McpOAuthController;
use App\Http\Controllers\OAuth\RegisterClientController;
use App\Http\Middleware\AddMcpOAuthChallenge;
use App\Http\Middleware\EnsureAdminMcpOAuthAccess;
use App\Http\Middleware\EnsureMcpOAuthRequest;
use App\Http\Middleware\RejectNonAdminMcpOAuthScope;
use App\Mcp\Servers\AdminNativePhpServer;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Facades\Mcp;
use Laravel\Mcp\Server\Middleware\AddWwwAuthenticateHeader;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\DenyAuthorizationController;

Mcp::web('/mcp/oauth/admin', AdminNativePhpServer::class)
    ->withoutMiddleware(AddWwwAuthenticateHeader::class)
    ->middleware([AddMcpOAuthChallenge::class, 'auth:oauth', EnsureAdminMcpOAuthAccess::class])
    ->name('mcp.oauth.admin');

Route::get('/.well-known/oauth-protected-resource/mcp/oauth/admin', [McpOAuthController::class, 'adminProtectedResource'])
    ->name('mcp.oauth.admin.protected-resource');
Route::get('/.well-known/oauth-authorization-server', [McpOAuthController::class, 'authorizationServer']);

Route::prefix('oauth')->group(function (): void {
    Route::post('/register', RegisterClientController::class)->middleware('throttle:20,1');
    Route::post('/token', [AccessTokenController::class, 'issueToken'])
        ->middleware(['throttle:60,1', EnsureMcpOAuthRequest::class])->name('passport.token');

    Route::middleware('web')->group(function (): void {
        Route::get('/authorize', [AuthorizationController::class, 'authorize'])
            ->middleware([EnsureMcpOAuthRequest::class, RejectNonAdminMcpOAuthScope::class])
            ->name('passport.authorizations.authorize');

        Route::middleware('auth:web')->group(function (): void {
            Route::post('/authorize', [ApproveAuthorizationController::class, 'approve'])
                ->middleware(RejectNonAdminMcpOAuthScope::class)
                ->name('passport.authorizations.approve');
            Route::delete('/authorize', [DenyAuthorizationController::class, 'deny'])->name('passport.authorizations.deny');
            Route::delete('/tokens/{token}', [McpOAuthController::class, 'revoke'])->name('mcp.oauth.revoke');
        });
    });
});
