<?php

namespace Database\Factories;

use App\Enums\PriceTier;
use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PluginPrice>
 */
class PluginPriceFactory extends Factory
{
    protected $model = PluginPrice::class;

    public function definition(): array
    {
        return [
            'plugin_id' => Plugin::factory(),
            'tier' => PriceTier::Regular,
            'amount' => fake()->randomElement([999, 1999, 2999, 4999, 9999]),
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
