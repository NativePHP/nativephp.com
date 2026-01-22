<?php

namespace App\Enums;

enum PriceTier: string
{
    case Regular = 'regular';
    case Subscriber = 'subscriber';
    case Eap = 'eap';

    public function label(): string
    {
        return match ($this) {
            self::Regular => 'Regular',
            self::Subscriber => 'Pro/Max Subscriber',
            self::Eap => 'Early Access',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Regular => 'Standard pricing for all customers',
            self::Subscriber => 'Discounted pricing for Pro and Max license holders',
            self::Eap => 'Special pricing for Early Access Program customers',
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
            self::Subscriber => 2,
            self::Regular => 3,
        };
    }
}
