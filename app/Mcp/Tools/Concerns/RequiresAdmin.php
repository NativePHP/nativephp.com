<?php

namespace App\Mcp\Tools\Concerns;

use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

trait RequiresAdmin
{
    protected function user(Request $request): ?User
    {
        /** @var User|null */
        return $request->user();
    }

    protected function ensureAdmin(Request $request): ?Response
    {
        if (! $this->user($request)?->isAdmin()) {
            return Response::error('This tool is only available to NativePHP site admins.');
        }

        return null;
    }

    /**
     * @param  array<array-key, mixed>  $payload
     */
    protected function toJson(array $payload): string
    {
        return (string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Strip secret-looking keys from nested arrays before returning metadata.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    protected function metadataOnly(array $data): array
    {
        $denied = [
            'password',
            'remember_token',
            'github_token',
            'key',
            'license_key',
            'plugin_license_key',
            'stripe_id',
            'pm_type',
            'pm_last_four',
            'secret',
            'api_key',
            'token',
        ];

        $clean = [];

        foreach ($data as $key => $value) {
            $normalized = strtolower((string) $key);

            if (in_array($normalized, $denied, true)
                || str_contains($normalized, 'secret')
                || str_contains($normalized, 'password')
                || (str_contains($normalized, 'token') && $normalized !== 'token_type')
                || str_ends_with($normalized, '_key') && $normalized !== 'key_id') {
                continue;
            }

            $clean[$key] = is_array($value) ? $this->metadataOnly($value) : $value;
        }

        return $clean;
    }
}
