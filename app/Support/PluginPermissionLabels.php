<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\AndroidPermission;
use App\Enums\IosBackgroundMode;
use Illuminate\Support\Str;

final class PluginPermissionLabels
{
    public static function androidPermission(string $permission): string
    {
        return AndroidPermission::tryFrom($permission)?->label() ?? Str::of($permission)
            ->after('android.permission.')
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    public static function iosBackgroundMode(string $mode): string
    {
        return IosBackgroundMode::tryFrom($mode)?->label() ?? Str::title(str_replace('-', ' ', $mode));
    }

    public static function iosCapability(string $capability): string
    {
        return Str::title(str_replace('-', ' ', $capability));
    }

    public static function iosEntitlement(string $entitlement): string
    {
        return Str::of($entitlement)->afterLast('.')->replace('-', ' ')->title()->toString();
    }
}
