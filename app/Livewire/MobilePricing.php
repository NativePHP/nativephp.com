<?php

namespace App\Livewire;

use App\Enums\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class MobilePricing extends Component
{
    #[Url]
    public string $interval = 'month';

    /** @var array{amount_due: string, raw_amount_due: int, credit: string, new_charge: string}|null */
    public ?array $upgradePreview = null;

    #[Locked]
    public $user;

    public function mount(): void
    {
        if (request()->has('email')) {
            $this->user = $this->findOrCreateUser(request()->query('email'));
        }
    }

    #[On('purchase-request-submitted')]
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
            Log::error('Failed to create checkout session. User does not exist and user is not authenticated.');

            return;
        }

        if (! ($subscription = Subscription::tryFrom($plan))) {
            Log::error('Failed to create checkout session. Invalid subscription plan name provided.');

            return;
        }

        $user->createOrGetStripeCustomer();

        $checkout = $user
            ->newSubscription('default', $subscription->stripePriceId(forceEap: $user->isEapCustomer(), interval: $this->interval))
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

    public function previewUpgrade(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->active()) {
            return;
        }

        $newPriceId = Subscription::Max->stripePriceId(forceEap: $user->isEapCustomer(), interval: $this->interval);

        try {
            $invoice = $subscription->previewInvoice($newPriceId);

            $currency = $invoice->asStripeInvoice()->currency;
            $credit = 0;
            $newCharge = 0;

            foreach ($invoice->invoiceLineItems() as $item) {
                $amount = $item->asStripeInvoiceLineItem()->amount;

                if ($amount < 0) {
                    $credit += abs($amount);
                } else {
                    $newCharge += $amount;
                }
            }

            $this->upgradePreview = [
                'amount_due' => $invoice->amountDue(),
                'raw_amount_due' => $invoice->rawAmountDue(),
                'credit' => Cashier::formatAmount($credit, $currency),
                'new_charge' => Cashier::formatAmount($newCharge, $currency),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to preview upgrade invoice', ['error' => $e->getMessage()]);
            $this->upgradePreview = null;
        }
    }

    public function upgradeSubscription(): mixed
    {
        $user = Auth::user();

        if (! $user) {
            Log::error('Failed to upgrade subscription. User is not authenticated.');

            return null;
        }

        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->active()) {
            Log::error('Failed to upgrade subscription. No active subscription found.');

            return null;
        }

        $newPriceId = Subscription::Max->stripePriceId(forceEap: $user->isEapCustomer(), interval: $this->interval);

        $subscription->skipTrial()->swapAndInvoice($newPriceId);

        return redirect(route('customer.dashboard'))->with('success', 'Your subscription has been upgraded to Ultra!');
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
        $hasExistingSubscription = false;
        $currentPlanName = null;
        $isAlreadyUltra = false;
        $isEapCustomer = false;
        $eapYearlyPrice = null;
        $eapDiscountPercent = null;
        $eapSavingsVsMonthly = null;
        $regularYearlyPrice = config('subscriptions.plans.max.price_yearly');

        if ($user = Auth::user()) {
            $isEapCustomer = $user->isEapCustomer();

            if ($isEapCustomer) {
                $eapYearlyPrice = config('subscriptions.plans.max.eap_price_yearly');
                $eapDiscountPercent = (int) round((1 - $eapYearlyPrice / $regularYearlyPrice) * 100);
                $eapSavingsVsMonthly = (config('subscriptions.plans.max.price_monthly') * 12) - $eapYearlyPrice;
            }

            $subscription = $user->subscription('default');

            if ($subscription && $subscription->active()) {
                $hasExistingSubscription = true;
                $isAlreadyUltra = $user->hasActiveUltraSubscription();

                try {
                    $currentPlanName = Subscription::fromStripePriceId(
                        $subscription->items->first()?->stripe_price ?? $subscription->stripe_price
                    )->name();
                } catch (\Exception $e) {
                    $currentPlanName = 'your current plan';
                }
            }
        }

        return view('livewire.mobile-pricing', [
            'hasExistingSubscription' => $hasExistingSubscription,
            'currentPlanName' => $currentPlanName,
            'isAlreadyUltra' => $isAlreadyUltra,
            'isEapCustomer' => $isEapCustomer,
            'eapYearlyPrice' => $eapYearlyPrice,
            'eapDiscountPercent' => $eapDiscountPercent,
            'eapSavingsVsMonthly' => $eapSavingsVsMonthly,
            'regularYearlyPrice' => $regularYearlyPrice,
        ]);
    }
}
