<?php

declare(strict_types=1);

namespace App\Enums;

enum PluginReportStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Resolved => 'Resolved',
            self::Dismissed => 'Dismissed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'danger',
            self::Resolved => 'success',
            self::Dismissed => 'gray',
        };
    }
}
