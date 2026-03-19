<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WallOfLoveSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users or create new ones
        $existingUsers = \App\Models\User::query()
            ->whereHas('licenses', function ($query) {
                $query->where('created_at', '<', '2025-06-01');
            })
            ->get();

        // If we have existing early adopter users, use them
        if ($existingUsers->count() >= 5) {
            $users = $existingUsers;
        } else {
            // Create some users with simple licenses (without subscription_item_id)
            $users = \App\Models\User::factory()
                ->count(10)
                ->create()
                ->each(function ($user) {
                    // Give each user an early adopter license (before June 1st, 2025)
                    \App\Models\License::factory()->create([
                        'user_id' => $user->id,
                        'subscription_item_id' => null, // Skip the subscription item relationship
                        'created_at' => fake()->dateTimeBetween('2024-01-01', '2025-05-31'),
                        'updated_at' => fake()->dateTimeBetween('2024-01-01', '2025-05-31'),
                    ]);
                });
        }

        // Get or create an admin user for approvals
        $admin = \App\Models\User::query()
            ->where('email', 'admin@example.com')
            ->first() ?? \App\Models\User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);

        // Create approved submissions (will be displayed on the wall of love page)
        \App\Models\WallOfLoveSubmission::factory()
            ->count(15)
            ->approved()
            ->create([
                'user_id' => fn () => $users->random()->id,
                'approved_by' => $admin->id,
            ]);

        // Create pending submissions (waiting for approval)
        \App\Models\WallOfLoveSubmission::factory()
            ->count(5)
            ->pending()
            ->create([
                'user_id' => fn () => $users->random()->id,
            ]);
    }
}
