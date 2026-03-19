<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductLicense>
 */
class ProductLicenseFactory extends Factory
{
    protected $model = ProductLicense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'stripe_invoice_id' => 'in_'.$this->faker->uuid(),
            'stripe_payment_intent_id' => 'pi_'.$this->faker->uuid(),
            'price_paid' => $this->faker->numberBetween(1000, 10000),
            'currency' => 'USD',
            'purchased_at' => now(),
        ];
    }
}
