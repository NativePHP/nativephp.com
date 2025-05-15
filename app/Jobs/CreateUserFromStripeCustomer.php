<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Stripe\Customer;

class CreateUserFromStripeCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Customer $customer) {}

    public function handle(): void
    {
        /** @var User $user */
        if ($user = Cashier::findBillable($this->customer)) {
            Log::debug("A user [{$user->id} | {$user->email}] with stripe_id [{$this->customer->id}] already exists.");

            return;
        }

        $user = User::query()->where('email', $this->customer->email)->first();

        if ($user && filled($user->stripe_id)) {
            // This could occur if a user performs/attempts multiple checkouts with the same email address.
            // In the event all existing stripe customers for this email address do NOT have an active
            // subscription, we could theoretically update the stripe_id for the existing user
            // and continue. However, for now, we will throw an exception.
            $this->fail("A user with email [{$user->email}] already exists but the current stripe_id [{$user->stripe_id}] does not match the new customer id [{$this->customer->id}].");

            return;
        }

        if ($user) {
            $user->stripe_id = $this->customer->id;
            $user->save();

            return;
        }

        Validator::validate(['email' => $this->customer->email], [
            'email' => 'required|email|max:255',
        ]);

        $user = new User;
        $user->name = $this->customer->name;
        $user->email = $this->customer->email;
        $user->stripe_id = $this->customer->id;
        // We will create a random password for the user and expect them to reset it.
        $user->password = Hash::make(Str::random(72));

        $user->save();
    }
}
