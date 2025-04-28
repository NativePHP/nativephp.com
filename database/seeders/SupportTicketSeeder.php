<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupportTicket::factory()
            ->count(10)
            ->has(
                SupportTicket\Reply::factory()
                    ->state(['user_id' => 1])
                    ->count(5)
            )
            ->create([
                'user_id' => 1,
                'status' => 'open',
            ]);
    }
}
