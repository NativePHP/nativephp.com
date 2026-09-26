<?php

declare(strict_types=1);

namespace App\Enums;

enum DocsScreenshotPlatform: string
{
    case Ios = 'ios';
    case Android = 'android';

    /**
     * Parse the `--platform` option ('ios', 'android', or 'both') into the
     * concrete platforms to capture. Returns an empty array for anything else.
     *
     * @return list<self>
     */
    public static function fromOption(string $value): array
    {
        return match ($value) {
            'ios' => [self::Ios],
            'android' => [self::Android],
            'both' => [self::Ios, self::Android],
            default => [],
        };
    }
}
