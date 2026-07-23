<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Held = 'held';
    case Pending = 'pending';
    case Transferred = 'transferred';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Held => 'Held',
            self::Pending => 'Pending',
            self::Transferred => 'Transferred',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Held => 'gray',
            self::Pending => 'yellow',
            self::Transferred => 'green',
            self::Failed => 'red',
        };
    }
}
