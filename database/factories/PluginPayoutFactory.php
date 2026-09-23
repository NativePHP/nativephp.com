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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gross = $this->faker->numberBetween(1000, 10000);
        $split = PluginPayout::calculateSplit($gross);

        return [
            'plugin_license_id' => PluginLicense::factory(),
            'developer_account_id' => DeveloperAccount::factory(),
            'gross_amount' => $gross,
            'platform_fee' => $split['platform_fee'],
            'developer_amount' => $split['developer_amount'],
            'status' => PayoutStatus::Pending,
            'eligible_for_payout_at' => now()->subDay(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => PayoutStatus::Pending]);
    }

    public function transferred(): static
    {
        return $this->state(fn () => [
            'status' => PayoutStatus::Transferred,
            'stripe_transfer_id' => 'tr_'.$this->faker->uuid(),
            'transferred_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => PayoutStatus::Failed,
            'failure_reason' => 'Stripe error: insufficient funds',
            'attempt_count' => 1,
            'last_attempted_at' => now(),
        ]);
    }
}
