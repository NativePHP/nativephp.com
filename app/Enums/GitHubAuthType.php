<?php

namespace App\Enums;

enum GitHubAuthType: string
{
    case OAuth = 'oauth';
    case App = 'app';

    public function label(): string
    {
        return match ($this) {
            self::OAuth => 'OAuth App',
            self::App => 'GitHub App',
        };
    }
}
