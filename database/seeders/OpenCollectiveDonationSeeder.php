<?php

namespace Database\Seeders;

use App\Models\OpenCollectiveDonation;
use Illuminate\Database\Seeder;

class OpenCollectiveDonationSeeder extends Seeder
{
    public function run(): void
    {
        // Create a few unclaimed donations
        OpenCollectiveDonation::factory()->count(3)->create();

        // Create a couple of claimed donations
        OpenCollectiveDonation::factory()
            ->count(2)
            ->claimed()
            ->create();

        // Create one with a specific order ID for easy testing
        OpenCollectiveDonation::factory()->create([
            'order_id' => 12345,
            'from_collective_name' => 'Test Donor',
            'from_collective_slug' => 'test-donor',
            'amount' => 5000,
        ]);
    }
}
