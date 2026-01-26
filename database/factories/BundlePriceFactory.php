<?php

namespace Database\Factories;

use App\Enums\PriceTier;
use App\Models\BundlePrice;
use App\Models\PluginBundle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BundlePrice>
 */
class BundlePriceFactory extends Factory
{
    protected $model = BundlePrice::class;

    public function definition(): array
    {
        return [
            'plugin_bundle_id' => PluginBundle::factory(),
            'tier' => PriceTier::Regular,
            'amount' => fake()->randomElement([4999, 9999, 14999, 19999]),
            'currency' => 'USD',
            'is_active' => true,
        ];
    }

    public function regular(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => PriceTier::Regular,
        ]);
    }

    public function subscriber(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => PriceTier::Subscriber,
        ]);
    }

    public function eap(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => PriceTier::Eap,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function amount(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }
}
