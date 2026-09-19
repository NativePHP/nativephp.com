<?php

namespace App\Http\Controllers\OAuth;

use App\Enums\TokenAbility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController;

class RegisterClientController extends OAuthRegisterController
{
    public function __invoke(Request $request): JsonResponse
    {
        $response = parent::__invoke($request);

        if ($response->getStatusCode() === 201) {
            $data = $response->getData(true);
            $data['scope'] = TokenAbility::AdminMcpServer->value;
            $response->setData($data);
        }

        return $response;
    }

    protected function isValidRedirectUri(string $value): bool
    {
        $parts = parse_url($value);

        if ($parts === false || isset($parts['fragment']) || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }

        if (($parts['scheme'] ?? null) === 'http'
            && ! in_array($parts['host'] ?? null, ['127.0.0.1', '[::1]', 'localhost'], true)) {
            return false;
        }

        return parent::isValidRedirectUri($value);
    }
}
