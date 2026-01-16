<?php

namespace App\Enums;

use App\Models\Plugin;

enum GrandfatheringTier: string
{
    case None = 'none';
    case Discounted = 'discounted';
    case FreeOfficialPlugins = 'free_official_plugins';

    public function label(): string
    {
        return match ($this) {
            self::None => 'No Discount',
            self::Discounted => 'Legacy Discount',
            self::FreeOfficialPlugins => 'Free Official Plugins',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::None => 'gray',
            self::Discounted => 'blue',
            self::FreeOfficialPlugins => 'green',
        };
    }

    public function getDiscountPercent(): int
    {
        return match ($this) {
            self::None => 0,
            self::Discounted => 20,
            self::FreeOfficialPlugins => 100,
        };
    }

    public function appliesToPlugin(Plugin $plugin): bool
    {
        return match ($this) {
            self::None => false,
            self::Discounted => true,
            self::FreeOfficialPlugins => $plugin->is_official,
        };
    }
}
