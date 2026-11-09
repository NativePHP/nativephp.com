<?php

declare(strict_types=1);

namespace App\Enums;

enum DocsCoverageCategory: string
{
    case ConfigKey = 'config_key';
    case ConsoleCommand = 'console_command';

    public function label(): string
    {
        return match ($this) {
            self::ConfigKey => 'Config key',
            self::ConsoleCommand => 'Console command',
        };
    }
}
