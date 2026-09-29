<?php

namespace App\Support\OAuth;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class McpBearerTokenValidator extends BearerTokenValidator
{
    public function validateAuthorization(ServerRequestInterface $request): ServerRequestInterface
    {
        $validated = parent::validateAuthorization($request);
        $jwt = trim((string) preg_replace('/^\s*Bearer\s/i', '', $request->getHeaderLine('authorization')));
        $token = (new Parser(new JoseEncoder))->parse($jwt);

        if (! $token instanceof UnencryptedToken) {
            throw OAuthServerException::accessDenied('An unencrypted access token is required.');
        }

        $claims = $token->claims();
        $audiences = $claims->get('aud', []);
        $audiences = is_array($audiences) ? $audiences : [$audiences];

        if (array_intersect(McpAccessToken::resources(), $audiences) === []
            || $claims->get('iss', null) !== rtrim(config('app.url'), '/')) {
            throw OAuthServerException::accessDenied('This access token was not issued for the NativePHP admin OAuth MCP resource.');
        }

        return $validated;
    }
}
