<?php

namespace App\Enums;

enum PluginActivityType: string
{
    case Submitted = 'submitted';
    case Resubmitted = 'resubmitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case DescriptionUpdated = 'description_updated';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Submitted',
            self::Resubmitted => 'Resubmitted',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::DescriptionUpdated => 'Description Updated',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Submitted => 'info',
            self::Resubmitted => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::DescriptionUpdated => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Submitted => 'heroicon-o-paper-airplane',
            self::Resubmitted => 'heroicon-o-arrow-path',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::DescriptionUpdated => 'heroicon-o-pencil-square',
        };
    }
}
