<?php

namespace Tests\Feature;

use App\Livewire\MobilePricing;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobilePricingTest extends TestCase
{
    use RefreshDatabase;

    private const PRO_PRICE_ID = 'price_test_pro_yearly';

    private const MAX_PRICE_ID = 'price_test_max_yearly';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'subscriptions.plans.pro.stripe_price_id' => self::PRO_PRICE_ID,
            'subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID,
        ]);
    }

    #[Test]
    public function authenticated_users_without_subscription_see_checkout_button()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $component = Livewire::test(MobilePricing::class);
        $component->assertSeeHtml([
            'wire:click="createCheckoutSession(\'max\')"',
        ]);
        $component->assertDontSeeHtml([
            '@click="$dispatch(\'open-purchase-modal\', { plan: \'max\' })"',
        ]);
    }

    #[Test]
    public function guest_users_see_purchase_modal_component()
    {
        Auth::logout();

        Livewire::test(MobilePricing::class)
            ->assertSeeLivewire('purchase-modal')
            ->assertSeeHtml([
                '@click="$dispatch(\'open-purchase-modal\', { plan: \'max\' })"',
            ])
            ->assertDontSeeHtml([
                'wire:click="createCheckoutSession(\'max\')"',
            ]);
    }

    #[Test]
    public function authenticated_users_do_not_see_purchase_modal_component()
    {
        Auth::login(User::factory()->create());

        Livewire::test(MobilePricing::class)
            ->assertDontSeeLivewire('purchase-modal');
    }

    #[Test]
    public function it_validates_email_before_creating_user()
    {
        Livewire::test(MobilePricing::class)
            ->call('handlePurchaseRequest', ['email' => 'invalid-email'])
            ->assertHasErrors('email');
    }

    #[Test]
    public function default_interval_is_month()
    {
        Livewire::test(MobilePricing::class)
            ->assertSet('interval', 'month');
    }

    #[Test]
    public function interval_can_be_set_to_year()
    {
        Livewire::test(MobilePricing::class)
            ->set('interval', 'year')
            ->assertSet('interval', 'year');
    }

    #[Test]
    public function existing_subscriber_sees_upgrade_button()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->assertSee('Upgrade to Ultra')
            ->assertDontSeeHtml('wire:click="createCheckoutSession(\'max\')"');
    }

    #[Test]
    public function ultra_subscriber_sees_already_on_ultra_message()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => self::MAX_PRICE_ID,
                'is_comped' => false,
            ]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::MAX_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->assertSee('on Ultra', escape: false)
            ->assertDontSee('Upgrade to Ultra')
            ->assertDontSeeHtml('wire:click="createCheckoutSession(\'max\')"');
    }

    #[Test]
    public function comped_max_subscriber_sees_upgrade_button()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => self::MAX_PRICE_ID,
                'is_comped' => true,
            ]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::MAX_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->assertSee('Upgrade to Ultra')
            ->assertDontSee('on Ultra', escape: false);
    }

    #[Test]
    public function upgrade_modal_shows_confirm_button_for_existing_subscriber()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->assertSeeHtml('wire:click="upgradeSubscription"')
            ->assertSee('Confirm upgrade');
    }

    #[Test]
    public function upgrade_modal_not_shown_for_users_without_subscription()
    {
        $user = User::factory()->create();
        Auth::login($user);

        Livewire::test(MobilePricing::class)
            ->assertDontSeeHtml('wire:click="upgradeSubscription"')
            ->assertDontSee('Confirm upgrade');
    }

    #[Test]
    public function eap_customer_sees_eap_offer_badge_on_annual_toggle()
    {
        $user = User::factory()->create();
        License::factory()->eapEligible()->withoutSubscriptionItem()->for($user)->create();
        Auth::login($user);

        Livewire::test(MobilePricing::class)
            ->assertSee('EAP offer')
            ->assertDontSee('Save 16%');
    }

    #[Test]
    public function non_eap_customer_sees_save_badge_on_annual_toggle()
    {
        $user = User::factory()->create();
        Auth::login($user);

        Livewire::test(MobilePricing::class)
            ->assertSee('Save 16%')
            ->assertDontSee('EAP offer');
    }

    #[Test]
    public function guest_sees_save_badge_on_annual_toggle()
    {
        Auth::logout();

        Livewire::test(MobilePricing::class)
            ->assertSee('Save 16%')
            ->assertDontSee('EAP offer');
    }

    #[Test]
    public function eap_customer_sees_strikethrough_and_discounted_price()
    {
        $user = User::factory()->create();
        License::factory()->eapEligible()->withoutSubscriptionItem()->for($user)->create();
        Auth::login($user);

        $eapPrice = config('subscriptions.plans.max.eap_price_yearly');
        $regularPrice = config('subscriptions.plans.max.price_yearly');
        $discount = (int) round((1 - $eapPrice / $regularPrice) * 100);

        Livewire::test(MobilePricing::class)
            ->assertSee('$'.$regularPrice.'/yr')
            ->assertSee($discount.'% off')
            ->assertSee('Early Access discount');
    }

    #[Test]
    public function non_eap_customer_does_not_see_eap_pricing()
    {
        $user = User::factory()->create();
        License::factory()->afterEap()->withoutSubscriptionItem()->for($user)->create();
        Auth::login($user);

        Livewire::test(MobilePricing::class)
            ->assertDontSee('Early Access discount')
            ->assertDontSee('EAP offer')
            ->assertSee('Save 16%');
    }

    #[Test]
    public function eap_upgrade_modal_shows_eap_discount_applied()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        License::factory()->eapEligible()->withoutSubscriptionItem()->for($user)->create();
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->assertSee('EAP discount applied');
    }

    #[Test]
    public function upgrade_button_triggers_preview_for_existing_subscriber()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->assertSeeHtml('wire:click="previewUpgrade"');
    }

    #[Test]
    public function upgrade_modal_shows_proration_breakdown_when_preview_loaded()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->set('upgradePreview', [
                'amount_due' => '$28.50',
                'raw_amount_due' => 2850,
                'new_charge' => '$35.00',
                'is_prorated' => false,
                'credit' => '$6.50',
                'remaining_credit' => null,
            ])
            ->assertSee('Due today')
            ->assertSee('$28.50')
            ->assertSee('$6.50')
            ->assertSee('$35.00')
            ->assertSee('Credit for unused')
            ->assertDontSee('pro-rated')
            ->assertDontSee('credited to your next invoice');
    }

    #[Test]
    public function upgrade_modal_shows_prorated_label_when_charge_is_prorated()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->set('upgradePreview', [
                'amount_due' => '$200.00',
                'raw_amount_due' => 20000,
                'new_charge' => '$250.00',
                'is_prorated' => true,
                'credit' => '$50.00',
                'remaining_credit' => null,
            ])
            ->assertSee('New plan (Ultra)')
            ->assertSee('pro-rated')
            ->assertSee('$250.00')
            ->assertSee('Credit for unused')
            ->assertSee('$50.00')
            ->assertSee('$200.00')
            ->assertDontSee('credited to your next invoice');
    }

    #[Test]
    public function upgrade_modal_shows_remaining_credit_note_when_credit_exceeds_charge()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->set('upgradePreview', [
                'amount_due' => '$0.00',
                'raw_amount_due' => 0,
                'new_charge' => '$35.00',
                'is_prorated' => false,
                'credit' => '$50.00',
                'remaining_credit' => '$15.00',
            ])
            ->assertSee('$0.00')
            ->assertSee('$35.00')
            ->assertSee('$50.00')
            ->assertSee('$15.00 will be credited to your next invoice');
    }

    #[Test]
    public function upgrade_modal_shows_fallback_when_preview_is_null()
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);
        Auth::login($user);

        $subscription = Cashier::$subscriptionModel::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Cashier::$subscriptionItemModel::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        Livewire::test(MobilePricing::class)
            ->set('upgradePreview', null)
            ->assertSee('Unable to load pricing preview')
            ->assertSee('Confirm upgrade');
    }

    #[Test]
    public function non_subscriber_does_not_see_preview_upgrade_button()
    {
        $user = User::factory()->create();
        Auth::login($user);

        Livewire::test(MobilePricing::class)
            ->assertDontSeeHtml('wire:click="previewUpgrade"');
    }
}
