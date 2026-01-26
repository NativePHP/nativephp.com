<?php

namespace App\Enums;

enum StripeConnectStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Onboarding',
            self::Active => 'Active',
            self::Disabled => 'Disabled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Active => 'green',
            self::Disabled => 'red',
        };
    }

    public function canReceivePayouts(): bool
    {
        return $this === self::Active;
    }
}
