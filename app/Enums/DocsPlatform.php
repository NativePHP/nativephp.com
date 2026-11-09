<?php

declare(strict_types=1);

namespace App\Enums;

enum DocsPlatform: string
{
    case Desktop = 'desktop';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::Desktop => 'NativePHP for Desktop',
            self::Mobile => 'NativePHP for Mobile',
        };
    }

    public static function tryFromRoute(): ?self
    {
        return self::tryFrom((string) request()->route('platform'));
    }
}
