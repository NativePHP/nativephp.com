<?php

namespace App\Support\OAuth;

use App\Enums\TokenAbility;
use DateTimeImmutable;
use Laravel\Passport\Bridge\AccessToken;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKeyInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class McpAccessToken extends AccessToken
{
    private CryptKeyInterface $signingKey;

    public static function adminResource(): string
    {
        return rtrim(config('app.url'), '/').'/mcp/oauth/admin';
    }

    /**
     * @return list<string>
     */
    public static function resources(): array
    {
        return [self::adminResource()];
    }

    public static function resource(): string
    {
        return self::adminResource();
    }

    public static function resourceForScope(string $scope): ?string
    {
        return match ($scope) {
            TokenAbility::AdminMcpServer->value => self::adminResource(),
            default => null,
        };
    }

    public static function scopeForResource(string $resource): ?string
    {
        return match ($resource) {
            self::adminResource() => TokenAbility::AdminMcpServer->value,
            default => null,
        };
    }

    /**
     * @param  array<int, ScopeEntityInterface|string>  $scopes
     */
    public static function resourceForScopes(array $scopes): string
    {
        return self::adminResource();
    }

    public function setPrivateKey(#[\SensitiveParameter] CryptKeyInterface $privateKey): void
    {
        parent::setPrivateKey($privateKey);
        $this->signingKey = $privateKey;
    }

    public function toString(): string
    {
        $jwt = Configuration::forAsymmetricSigner(
            new Sha256,
            InMemory::plainText($this->signingKey->getKeyContents(), $this->signingKey->getPassPhrase() ?? ''),
            InMemory::plainText('unused'),
        );

        return $jwt->builder()
            ->permittedFor($this->getClient()->getIdentifier(), self::resourceForScopes($this->getScopes()))
            ->issuedBy(rtrim(config('app.url'), '/'))
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable)
            ->canOnlyBeUsedAfter(new DateTimeImmutable)
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo($this->getUserIdentifier() ?? $this->getClient()->getIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->getToken($jwt->signer(), $jwt->signingKey())
            ->toString();
    }
}
