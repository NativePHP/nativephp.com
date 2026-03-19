<?php

namespace Database\Factories;

use App\Enums\Subscription;
use App\Models\License;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use Laravel\Cashier\SubscriptionItem;

/**
 * @extends Factory<License>
 */
class LicenseFactory extends Factory
{
    protected $model = License::class;

    public function definition(): array
    {
        return [
            'anystack_id' => fake()->uuid(),
            'user_id' => User::factory(),
            'subscription_item_id' => SubscriptionItem::factory(),
            'policy_name' => fake()->randomElement(Subscription::cases())->value,
            'key' => fake()->uuid(),
            'is_suspended' => false,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fn (array $attrs) => $attrs['created_at'],
            'expires_at' => fn (array $attrs) => Date::parse($attrs['created_at'])->addYear(),
        ];
    }

    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'policy_name' => 'pro',
        ]);
    }

    public function max(): static
    {
        return $this->state(fn (array $attributes) => [
            'policy_name' => 'max',
        ]);
    }

    public function mini(): static
    {
        return $this->state(fn (array $attributes) => [
            'policy_name' => 'mini',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => false,
            'expires_at' => now()->addYear(),
        ]);
    }

    public function eapEligible(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => Date::create(2025, 5, 1),
            'updated_at' => Date::create(2025, 5, 1),
        ]);
    }

    public function afterEap(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => Date::create(2025, 7, 1),
            'updated_at' => Date::create(2025, 7, 1),
        ]);
    }

    public function withoutSubscriptionItem(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_item_id' => null,
        ]);
    }
}
