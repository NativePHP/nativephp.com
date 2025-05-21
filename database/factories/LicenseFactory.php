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
}
