<?php

namespace App\Enums;

enum PluginTier: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';

    public function label(): string
    {
        return match ($this) {
            self::Bronze => 'Bronze',
            self::Silver => 'Silver',
            self::Gold => 'Gold',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Bronze => 'Entry-level plugins',
            self::Silver => 'Standard plugins',
            self::Gold => 'Premium plugins',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Bronze => 'warning',
            self::Silver => 'gray',
            self::Gold => 'success',
        };
    }

    /**
     * Get the price amounts (in cents) for each PriceTier.
     *
     * @return array<string, int>
     */
    public function getPrices(): array
    {
        return match ($this) {
            self::Bronze => [
                PriceTier::Regular->value => 2900,
                PriceTier::Subscriber->value => 1100,
            ],
            self::Silver => [
                PriceTier::Regular->value => 4900,
                PriceTier::Subscriber->value => 1700,
            ],
            self::Gold => [
                PriceTier::Regular->value => 9900,
                PriceTier::Subscriber->value => 3100,
            ],
        };
    }
}
