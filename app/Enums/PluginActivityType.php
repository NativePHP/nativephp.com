<?php

namespace App\Enums;

enum PluginActivityType: string
{
    case Submitted = 'submitted';
    case Resubmitted = 'resubmitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case DescriptionUpdated = 'description_updated';
    case Withdrawn = 'withdrawn';
    case ReturnedToDraft = 'returned_to_draft';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Submitted',
            self::Resubmitted => 'Resubmitted',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::DescriptionUpdated => 'Description Updated',
            self::Withdrawn => 'Withdrawn',
            self::ReturnedToDraft => 'Returned to Draft',
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
            self::Withdrawn => 'warning',
            self::ReturnedToDraft => 'warning',
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
            self::Withdrawn => 'heroicon-o-arrow-uturn-left',
            self::ReturnedToDraft => 'heroicon-o-arrow-uturn-left',
        };
    }
}
