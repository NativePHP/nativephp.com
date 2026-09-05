<?php

namespace App\Enums;

enum PluginCategory: string
{
    case Media = 'media';
    case Security = 'security';
    case Connectivity = 'connectivity';
    case Notifications = 'notifications';
    case Payments = 'payments';
    case Analytics = 'analytics';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Media => 'Media',
            self::Security => 'Security',
            self::Connectivity => 'Connectivity',
            self::Notifications => 'Notifications',
            self::Payments => 'Payments',
            self::Analytics => 'Analytics',
            self::System => 'System',
        };
    }
}
