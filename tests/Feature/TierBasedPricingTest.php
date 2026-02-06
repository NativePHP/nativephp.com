<?php

namespace Tests\Feature;

use App\Enums\PriceTier;
use App\Features\ShowPlugins;
use App\Models\BundlePrice;
use App\Models\Cart;
use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginPrice;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TierBasedPricingTest extends TestCase
{
    use RefreshDatabase;

    // Test price IDs that match the Subscription enum's fromStripePriceId mapping
    private const MINI_PRICE_ID = 'price_test_mini';

    private const PRO_PRICE_ID = 'price_1RoZeVAyFo6rlwXqtnOViUCf';

    private const MAX_PRICE_ID = 'price_1RoZk0AyFo6rlwXqjkLj4hZ0';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);

        // Set config for mini price ID used in tests
        config(['subscriptions.plans.mini.stripe_price_id' => self::MINI_PRICE_ID]);
    }

    /**
     * Create an active subscription for a user.
     */
    private function createSubscription(User $user, string $priceId): Subscription
    {
        return Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => $priceId]);
    }

    // ========================================
    // User Tier Eligibility Tests
    // ========================================

    #[Test]
    public function user_without_license_only_qualifies_for_regular_tier(): void
    {
        $user = User::factory()->create();

        $tiers = $user->getEligiblePriceTiers();

        $this->assertCount(1, $tiers);
        $this->assertContains(PriceTier::Regular, $tiers);
        $this->assertNotContains(PriceTier::Subscriber, $tiers);
        $this->assertNotContains(PriceTier::Eap, $tiers);
    }

    #[Test]
    public function user_with_active_pro_subscription_qualifies_for_subscriber_tier(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);

        $tiers = $user->getEligiblePriceTiers();

        $this->assertTrue($user->subscribed());
        $this->assertFalse($user->isEapCustomer());
        $this->assertContains(PriceTier::Regular, $tiers);
        $this->assertContains(PriceTier::Subscriber, $tiers);
        $this->assertNotContains(PriceTier::Eap, $tiers);
    }

    #[Test]
    public function user_with_active_max_subscription_qualifies_for_subscriber_tier(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MAX_PRICE_ID);

        $tiers = $user->getEligiblePriceTiers();

        $this->assertTrue($user->subscribed());
        $this->assertFalse($user->isEapCustomer());
        $this->assertContains(PriceTier::Subscriber, $tiers);
    }

    #[Test]
    public function user_with_mini_subscription_qualifies_for_subscriber_tier(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MINI_PRICE_ID);

        $this->assertTrue($user->subscribed());

        $tiers = $user->getEligiblePriceTiers();

        $this->assertContains(PriceTier::Subscriber, $tiers);
    }

    #[Test]
    public function user_with_canceled_subscription_does_not_qualify_for_subscriber_tier(): void
    {
        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->canceled()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        $this->assertFalse($user->subscribed());

        $tiers = $user->getEligiblePriceTiers();

        $this->assertNotContains(PriceTier::Subscriber, $tiers);
    }

    #[Test]
    public function user_with_past_due_subscription_does_not_qualify_for_subscriber_tier(): void
    {
        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->pastDue()
            ->create(['stripe_price' => self::PRO_PRICE_ID]);

        $this->assertFalse($user->subscribed());

        $tiers = $user->getEligiblePriceTiers();

        $this->assertNotContains(PriceTier::Subscriber, $tiers);
    }

    #[Test]
    public function user_with_eap_license_qualifies_for_eap_tier(): void
    {
        $user = User::factory()->create();
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $this->assertTrue($user->isEapCustomer());

        $tiers = $user->getEligiblePriceTiers();

        $this->assertContains(PriceTier::Eap, $tiers);
    }

    #[Test]
    public function user_with_subscription_and_eap_license_qualifies_for_both_tiers(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);
        // EAP eligibility is still determined by licenses table
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $tiers = $user->getEligiblePriceTiers();

        $this->assertTrue($user->subscribed());
        $this->assertTrue($user->isEapCustomer());
        $this->assertContains(PriceTier::Regular, $tiers);
        $this->assertContains(PriceTier::Subscriber, $tiers);
        $this->assertContains(PriceTier::Eap, $tiers);
        $this->assertCount(3, $tiers);
    }

    // ========================================
    // Plugin Tier Pricing Tests
    // ========================================

    #[Test]
    public function guest_user_sees_regular_plugin_price(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser(null);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    #[Test]
    public function user_without_license_sees_regular_plugin_price(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    #[Test]
    public function subscriber_sees_subscriber_plugin_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(1999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Subscriber, $bestPrice->tier);
    }

    #[Test]
    public function eap_customer_sees_eap_plugin_price(): void
    {
        $user = User::factory()->create();
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Eap, $bestPrice->tier);
    }

    #[Test]
    public function user_qualifying_for_multiple_tiers_sees_lowest_plugin_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);
        License::factory()
            ->for($user)
            ->pro()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(999, $bestPrice->amount);
    }

    #[Test]
    public function subscriber_sees_subscriber_price_when_it_is_lower_than_eap(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);
        License::factory()
            ->for($user)
            ->pro()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(500)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertEquals(500, $bestPrice->amount);
        $this->assertEquals(PriceTier::Subscriber, $bestPrice->tier);
    }

    #[Test]
    public function plugin_falls_back_to_regular_price_when_user_tier_not_available(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    #[Test]
    public function plugin_inactive_prices_are_not_returned(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->inactive()->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->regular()->amount(3999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(3999, $bestPrice->amount);
    }

    #[Test]
    public function get_regular_price_returns_regular_tier_price(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $regularPrice = $plugin->getRegularPrice();

        $this->assertNotNull($regularPrice);
        $this->assertEquals(2999, $regularPrice->amount);
        $this->assertEquals(PriceTier::Regular, $regularPrice->tier);
    }

    // ========================================
    // Bundle Tier Pricing Tests
    // ========================================

    #[Test]
    public function guest_user_sees_regular_bundle_price(): void
    {
        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->eap()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser(null);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(9999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    #[Test]
    public function user_without_license_sees_regular_bundle_price(): void
    {
        $user = User::factory()->create();
        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->eap()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(9999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    #[Test]
    public function subscriber_sees_subscriber_bundle_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MAX_PRICE_ID);

        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->eap()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(7999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Subscriber, $bestPrice->tier);
    }

    #[Test]
    public function eap_customer_sees_eap_bundle_price(): void
    {
        $user = User::factory()->create();
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->eap()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(4999, $bestPrice->amount);
        $this->assertEquals(PriceTier::Eap, $bestPrice->tier);
    }

    #[Test]
    public function user_qualifying_for_multiple_tiers_sees_lowest_bundle_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MAX_PRICE_ID);
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->eap()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(4999, $bestPrice->amount);
    }

    #[Test]
    public function bundle_falls_back_to_legacy_price_when_no_tier_prices_exist(): void
    {
        $user = User::factory()->create();
        $bundle = PluginBundle::factory()->active()->create(['price' => 14999]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNull($bestPrice);

        $regularPrice = $bundle->getRegularPrice();
        $this->assertNull($regularPrice);

        $this->assertEquals('$149.99', $bundle->formatted_price);
    }

    #[Test]
    public function get_regular_bundle_price_returns_regular_tier_price(): void
    {
        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $regularPrice = $bundle->getRegularPrice();

        $this->assertNotNull($regularPrice);
        $this->assertEquals(9999, $regularPrice->amount);
        $this->assertEquals(PriceTier::Regular, $regularPrice->tier);
    }

    // ========================================
    // Cart Tier Pricing Tests
    // ========================================

    #[Test]
    public function cart_adds_plugin_at_regular_price_for_user_without_license(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $cartService = new CartService;
        $item = $cartService->addPlugin($cart, $plugin);

        $this->assertEquals(2999, $item->price_at_addition);
        $this->assertEquals(PriceTier::Regular, $item->pluginPrice->tier);
    }

    #[Test]
    public function cart_adds_plugin_at_subscriber_price_for_pro_user(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $cartService = new CartService;
        $item = $cartService->addPlugin($cart, $plugin);

        $this->assertEquals(1999, $item->price_at_addition);
        $this->assertEquals(PriceTier::Subscriber, $item->pluginPrice->tier);
    }

    #[Test]
    public function cart_adds_plugin_at_eap_price_for_eap_customer(): void
    {
        $user = User::factory()->create();
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $cartService = new CartService;
        $item = $cartService->addPlugin($cart, $plugin);

        $this->assertEquals(999, $item->price_at_addition);
        $this->assertEquals(PriceTier::Eap, $item->pluginPrice->tier);
    }

    #[Test]
    public function cart_adds_bundle_at_regular_price_for_user_without_license(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach($plugin->id);
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $cartService = new CartService;
        $item = $cartService->addBundle($cart, $bundle);

        $this->assertEquals(9999, $item->bundle_price_at_addition);
    }

    #[Test]
    public function cart_adds_bundle_at_subscriber_price_for_max_user(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MAX_PRICE_ID);
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach($plugin->id);
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $cartService = new CartService;
        $item = $cartService->addBundle($cart, $bundle);

        $this->assertEquals(7999, $item->bundle_price_at_addition);
    }

    #[Test]
    public function cart_adds_bundle_at_eap_price_for_eap_customer(): void
    {
        $user = User::factory()->create();
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach($plugin->id);
        BundlePrice::factory()->regular()->amount(9999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->eap()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);

        $cartService = new CartService;
        $item = $cartService->addBundle($cart, $bundle);

        $this->assertEquals(4999, $item->bundle_price_at_addition);
    }

    #[Test]
    public function cart_refresh_prices_updates_to_current_tier_price(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        $regularPrice = PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cartService = new CartService;
        $item = $cartService->addPlugin($cart, $plugin);
        $this->assertEquals(2999, $item->price_at_addition);

        $this->createSubscription($user, self::PRO_PRICE_ID);

        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $user->refresh();
        $changes = $cartService->refreshPrices($cart->fresh());

        $this->assertCount(1, $changes);
        $this->assertEquals('price_changed', $changes[0]['type']);
        $this->assertEquals(2999, $changes[0]['old_price']);
        $this->assertEquals(1999, $changes[0]['new_price']);

        $item->refresh();
        $this->assertEquals(1999, $item->price_at_addition);
    }

    #[Test]
    public function cart_exchange_for_bundle_uses_correct_tier_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);
        $cart = Cart::factory()->for($user)->create();

        $plugin1 = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        $plugin2 = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin1->id]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin2->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach([$plugin1->id, $plugin2->id]);
        BundlePrice::factory()->regular()->amount(4999)->create(['plugin_bundle_id' => $bundle->id]);
        BundlePrice::factory()->subscriber()->amount(3999)->create(['plugin_bundle_id' => $bundle->id]);

        $cartService = new CartService;
        $cartService->addPlugin($cart, $plugin1);
        $cartService->addPlugin($cart, $plugin2);

        $this->assertEquals(2, $cart->items()->count());

        $bundleItem = $cartService->exchangeForBundle($cart->fresh(), $bundle);

        $this->assertEquals(1, $cart->fresh()->items()->count());
        $this->assertEquals(3999, $bundleItem->bundle_price_at_addition);
    }

    // ========================================
    // Edge Case Tests
    // ========================================

    #[Test]
    public function subscriber_who_cancels_subscription_sees_regular_price(): void
    {
        $user = User::factory()->create();
        $subscription = $this->createSubscription($user, self::PRO_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $this->assertEquals(1999, $plugin->getBestPriceForUser($user)->amount);

        $subscription->update(['stripe_status' => 'canceled', 'ends_at' => now()]);
        $user->refresh();

        $this->assertEquals(2999, $plugin->getBestPriceForUser($user)->amount);
    }

    #[Test]
    public function user_with_only_eap_tier_price_available_sees_that_price(): void
    {
        $user = User::factory()->create();
        License::factory()
            ->for($user)
            ->mini()
            ->active()
            ->eapEligible()
            ->withoutSubscriptionItem()
            ->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->eap()->amount(999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(999, $bestPrice->amount);
    }

    #[Test]
    public function regular_user_gets_null_when_no_regular_tier_price_exists(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNull($bestPrice);
        $this->assertFalse($plugin->hasAccessiblePriceFor($user));
    }

    #[Test]
    public function subscriber_can_access_plugin_with_only_subscriber_tier_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(1999, $bestPrice->amount);
        $this->assertTrue($plugin->hasAccessiblePriceFor($user));
    }

    #[Test]
    public function bundle_without_regular_tier_price_is_not_accessible_to_regular_user(): void
    {
        $user = User::factory()->create();

        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNull($bestPrice);
        $this->assertFalse($bundle->hasAccessiblePriceFor($user));
    }

    #[Test]
    public function subscriber_can_access_bundle_with_only_subscriber_tier_price(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MAX_PRICE_ID);

        $bundle = PluginBundle::factory()->active()->create();
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $bestPrice = $bundle->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(7999, $bestPrice->amount);
        $this->assertTrue($bundle->hasAccessiblePriceFor($user));
    }

    #[Test]
    public function free_plugin_is_always_accessible(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->approved()->free()->create(['is_active' => true]);

        $this->assertTrue($plugin->isFree());
        $this->assertNull($plugin->getBestPriceForUser($user));
    }

    // ========================================
    // Access Control Tests
    // ========================================

    #[Test]
    public function inaccessible_paid_plugin_returns_404(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertNotFound();
    }

    #[Test]
    public function accessible_paid_plugin_returns_200(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::PRO_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk();
    }

    #[Test]
    public function inaccessible_bundle_returns_404(): void
    {
        $user = User::factory()->create();

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach($plugin->id);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $this->actingAs($user)
            ->get(route('bundles.show', $bundle))
            ->assertNotFound();
    }

    #[Test]
    public function accessible_bundle_returns_200(): void
    {
        $user = User::factory()->create();
        $this->createSubscription($user, self::MAX_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create(['is_active' => true]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach($plugin->id);
        BundlePrice::factory()->subscriber()->amount(7999)->create(['plugin_bundle_id' => $bundle->id]);

        $this->actingAs($user)
            ->get(route('bundles.show', $bundle))
            ->assertOk();
    }
}
