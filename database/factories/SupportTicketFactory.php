<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'mask' => 'NATIVE-'.$this->faker->numberBetween(1000, 9999),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'status' => 'open',
            'product' => $this->faker->randomElement(['mobile', 'desktop', 'bifrost', 'nativephp.com']),
            'issue_type' => null,
            'metadata' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }

    public function forMobile(): static
    {
        return $this->state(fn () => [
            'product' => 'mobile',
            'metadata' => [
                'mobile_area' => 'camera',
                'trying_to_do' => $this->faker->sentence(),
                'what_happened' => $this->faker->sentence(),
                'reproduction_steps' => $this->faker->paragraph(),
                'environment' => 'iOS 17, iPhone 15',
            ],
        ]);
    }

    public function forBifrost(): static
    {
        return $this->state(fn () => [
            'product' => 'bifrost',
            'issue_type' => $this->faker->randomElement(['account_query', 'bug', 'feature_request', 'other']),
        ]);
    }
}
