<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MobilePricing extends Component
{
    #[Locked]
    public bool $discounted = false;

    #[Locked]
    public $user;

    protected $listeners = [
        'purchase-request-submitted' => 'handlePurchaseRequest',
    ];

    public function mount()
    {
        if (request()->has('email')) {
            $this->user = $this->findOrCreateUser(request()->query('email'));
        }
    }

    public function handlePurchaseRequest(array $data)
    {
        if (! $this->user) {
            $user = $this->findOrCreateUser($data['email']);
        }

        return $this->createCheckoutSession($data['plan'], $this->user ?? $user);
    }

    public function createCheckoutSession(?string $plan, ?User $user = null)
    {
        // This method is somehow getting called without a plan being passed in.
        // Not sure how (probably folks hacking or a bot thing),
        // but we will just return early when this happens.
        if (! $plan) {
            return;
        }

        // If a user isn't passed into this method, Livewire will instantiate
        // a new User. So we need to check that the user exists before using it,
        // and then use the authenticated user as a fallback.
        $user = $user?->exists ? $user : Auth::user();

        if (! $user) {
            // TODO: return a flash message or notification to the user that there
            //   was an error.
            Log::error('Failed to create checkout session. User does not exist and user is not authenticated.');

            return;
        }

        if (! ($subscription = Subscription::tryFrom($plan))) {
            // TODO: return a flash message or notification to the user that there
            //   was an error.
            Log::error('Failed to create checkout session. Invalid subscription plan name provided.');

            return;
        }

        $user->createOrGetStripeCustomer();

        $checkout = $user
            ->newSubscription('default', $subscription->stripePriceId(discounted: $this->discounted))
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => $this->successUrl(),
                'cancel_url' => route('pricing'),
                'consent_collection' => [
                    'terms_of_service' => 'required',
                ],
                'customer_update' => [
                    'name' => 'auto',
                    'address' => 'auto',
                ],
                'tax_id_collection' => [
                    'enabled' => true,
                ],
            ]);

        return redirect($checkout->url);
    }

    private function findOrCreateUser(string $email): User
    {
        Validator::validate(['email' => $email], [
            'email' => 'required|email|max:255',
        ]);

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
