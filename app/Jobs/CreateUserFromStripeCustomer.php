<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Stripe\Customer;

class CreateUserFromStripeCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Customer $customer) {}

    public function handle(): void
    {
        if (Cashier::findBillable($this->customer)) {
            $this->fail("A user already exists for Stripe customer [{$this->customer->id}].");

            return;
        }

        if (User::query()->where('email', $this->customer->email)->exists()) {
            $this->fail("A user already exists for email [{$this->customer->email}].");

            return;
        }

        $user = new User;
        $user->name = $this->customer->name;
        $user->email = $this->customer->email;
        $user->stripe_id = $this->customer->id;
        // We will create a random password for the user and expect them to reset it.
        $user->password = Hash::make(Str::random(72));

        $user->save();
    }
}
