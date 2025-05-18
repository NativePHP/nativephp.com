<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class MobilePricing extends Component
{
    protected $listeners = [
        'purchase-request-submitted' => 'handlePurchaseRequest',
    ];

    public function handlePurchaseRequest(array $data)
    {
        $user = $this->findOrCreateUser($data['email']);

        return $this->createCheckoutSession($data['plan'], $user);
    }

    public function createCheckoutSession(string $plan, ?User $user = null)
    {
        if (! ($user ??= Auth::user())) {
            return;
        }

        if (! ($subscription = Subscription::tryFrom($plan))) {
            return;
        }

        $user->createOrGetStripeCustomer();

        $checkout = $user
            ->newSubscription('default', $subscription->stripePriceId())
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => $this->successUrl(),
                'cancel_url' => route('early-adopter'),
            ]);

        return redirect($checkout->url);
    }

    private function findOrCreateUser(string $email): User
    {
        return User::firstOrCreate([
            'email' => $email,
        ], [
            'password' => Hash::make(Str::random(72)),
        ]);
    }

    private function successUrl(): string
    {
        // This is a hack to get the route() function to work. If you try
        // to pass {CHECKOUT_SESSION_ID} to the route function, it will
        // throw an error.
        return Str::replace(
            'abc',
            '{CHECKOUT_SESSION_ID}',
            route('order.success', ['checkoutSessionId' => 'abc'])
        );
    }

    public function render()
    {
        return view('livewire.mobile-pricing');
    }
}
