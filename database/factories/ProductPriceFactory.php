<?php

namespace Database\Factories;

use App\Enums\PriceTier;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    protected $model = ProductPrice::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
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
