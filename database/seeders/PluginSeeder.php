<?php

namespace Database\Seeders;

use App\Models\Plugin;
use App\Models\User;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users or create new ones for plugin submissions
        $users = User::query()->take(20)->get();

        if ($users->count() < 20) {
            $additionalUsers = User::factory()
                ->count(20 - $users->count())
                ->create();

            $users = $users->merge($additionalUsers);
        }

        // Get or create an admin user for approvals
        $admin = User::query()
            ->where('email', 'admin@example.com')
            ->first() ?? User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);

        // Create 10 featured approved plugins (free)
        Plugin::factory()
            ->count(10)
            ->approved()
            ->featured()
            ->free()
            ->create([
                'user_id' => fn () => $users->random()->id,
                'approved_by' => $admin->id,
            ]);

        // Create 5 featured approved plugins (paid)
        Plugin::factory()
            ->count(5)
            ->approved()
            ->featured()
            ->paid()
            ->create([
                'user_id' => fn () => $users->random()->id,
                'approved_by' => $admin->id,
            ]);

        // Create 30 approved free plugins (not featured)
        Plugin::factory()
            ->count(30)
            ->approved()
            ->free()
            ->create([
                'user_id' => fn () => $users->random()->id,
                'approved_by' => $admin->id,
            ]);

        // Create 15 approved paid plugins (not featured)
        Plugin::factory()
            ->count(15)
            ->approved()
            ->paid()
            ->create([
                'user_id' => fn () => $users->random()->id,
                'approved_by' => $admin->id,
            ]);

        // Create 20 pending plugins (mix of free and paid)
        Plugin::factory()
            ->count(15)
            ->pending()
            ->free()
            ->create([
                'user_id' => fn () => $users->random()->id,
            ]);

        Plugin::factory()
            ->count(5)
            ->pending()
            ->paid()
            ->create([
                'user_id' => fn () => $users->random()->id,
            ]);

        // Create 15 rejected plugins
        Plugin::factory()
            ->count(10)
            ->rejected()
            ->free()
            ->create([
                'user_id' => fn () => $users->random()->id,
            ]);

        Plugin::factory()
            ->count(5)
            ->rejected()
            ->paid()
            ->create([
                'user_id' => fn () => $users->random()->id,
            ]);

        // Create a few approved plugins without descriptions
        Plugin::factory()
            ->count(5)
            ->approved()
            ->free()
            ->withoutDescription()
            ->create([
                'user_id' => fn () => $users->random()->id,
                'approved_by' => $admin->id,
            ]);

        $this->command->info('Created plugins:');
        $this->command->info('  - 15 featured approved (10 free, 5 paid)');
        $this->command->info('  - 45 approved non-featured (30 free, 15 paid)');
        $this->command->info('  - 5 approved without descriptions');
        $this->command->info('  - 20 pending (15 free, 5 paid)');
        $this->command->info('  - 15 rejected (10 free, 5 paid)');
        $this->command->info('  Total: 100 plugins (65 approved)');
    }
}
