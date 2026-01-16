<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Pending = 'pending';
    case Transferred = 'transferred';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Transferred => 'Transferred',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Transferred => 'green',
            self::Failed => 'red',
        };
    }
}
