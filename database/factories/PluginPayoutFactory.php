<?php

namespace Database\Factories;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginPayout>
 */
class PluginPayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grossAmount = $this->faker->numberBetween(1000, 10000);
        $split = PluginPayout::calculateSplit($grossAmount);

        return [
            'plugin_license_id' => PluginLicense::factory(),
            'developer_account_id' => DeveloperAccount::factory(),
            'gross_amount' => $grossAmount,
            'platform_fee' => $split['platform_fee'],
            'developer_amount' => $split['developer_amount'],
            'status' => PayoutStatus::Pending,
        ];
    }

    public function transferred(): static
    {
        return $this->state(fn () => [
            'status' => PayoutStatus::Transferred,
            'stripe_transfer_id' => 'tr_'.$this->faker->uuid(),
            'transferred_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => PayoutStatus::Cancelled,
        ]);
    }
}
