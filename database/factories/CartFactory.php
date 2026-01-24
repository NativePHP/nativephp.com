<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => null,
            'expires_at' => now()->addDays(30),
        ];
    }

    public function forGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => fake()->uuid(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
