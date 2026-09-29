<?php

declare(strict_types=1);

namespace App\Enums;

enum PluginReportCategory: string
{
    case MaliciousCode = 'malicious_code';
    case UnresponsiveAuthor = 'unresponsive_author';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MaliciousCode => 'Malicious or Unsafe Code',
            self::UnresponsiveAuthor => 'Unresponsive Author',
            self::Other => 'Other',
        };
    }
}
