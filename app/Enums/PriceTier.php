<?php

namespace App\Enums;

enum PriceTier: string
{
    case Regular = 'regular';
    case Subscriber = 'subscriber';
    case Eap = 'eap';
    case Ultra = 'ultra';

    public function label(): string
    {
        return match ($this) {
            self::Regular => 'Regular',
            self::Subscriber => 'Subscriber',
            self::Eap => 'Early Access',
            self::Ultra => 'Ultra',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Regular => 'Standard pricing for all customers',
            self::Subscriber => 'Discounted pricing for subscribers',
            self::Eap => 'Special pricing for Early Access Program customers',
            self::Ultra => 'Discounted pricing for Ultra subscribers',
        };
    }

    /**
     * Get the priority order for tier selection (lower = higher priority).
     * When a user qualifies for multiple tiers, the lowest priced tier wins,
     * but this priority helps with display/sorting.
     */
    public function priority(): int
    {
        return match ($this) {
            self::Eap => 1,
            self::Ultra => 2,
            self::Subscriber => 3,
            self::Regular => 4,
        };
    }
}
