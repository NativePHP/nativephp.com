<?php

namespace App\Providers;

use App\Enums\TokenAbility;
use App\Support\OAuth\McpAccessToken;
use App\Support\OAuth\McpBearerTokenValidator;
use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\ResourceServer;

class McpOAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Passport::ignoreRoutes();

        $this->app->singleton(ResourceServer::class, function ($app): ResourceServer {
            $repository = $app->make(AccessTokenRepository::class);
            $key = str_replace('\\n', "\n", config('passport.public_key') ?? '')
                ?: 'file://'.Passport::keyPath('oauth-public.key');

            return new ResourceServer(
                $repository,
                new CryptKey($key, null, Passport::$validateKeyPermissions),
                new McpBearerTokenValidator($repository),
            );
        });
    }

    public function boot(): void
    {
        Passport::tokensCan([
            TokenAbility::AdminMcpServer->value => 'NativePHP site-admin MCP access (blog drafts, signups, support, plugins — no secrets)',
        ]);
        Passport::setDefaultScope([TokenAbility::AdminMcpServer->value]);
        Passport::tokensExpireIn(CarbonInterval::hour());
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::useAccessTokenEntity(McpAccessToken::class);
        Passport::authorizationView('mcp.authorize');
    }
}
