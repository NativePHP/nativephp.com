<?php

namespace Database\Factories\SupportTicket;

use App\Models\SupportTicket;
use App\Models\SupportTicket\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReplyFactory extends Factory
{
    protected $model = Reply::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->paragraphs(2, true),
            'attachments' => null,
            'note' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'support_ticket_id' => SupportTicket::factory(),
            'user_id' => User::factory(),
        ];
    }
}
