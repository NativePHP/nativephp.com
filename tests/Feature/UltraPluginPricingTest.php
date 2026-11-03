<?php

namespace Tests\Feature;

use App\Enums\PluginTier;
use App\Enums\PriceTier;
use App\Features\ShowPlugins;
use App\Models\Cart;
use App\Models\Plugin;
use App\Models\PluginPrice;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UltraPluginPricingTest extends TestCase
{
    use RefreshDatabase;

    private const ULTRA_PRICE_ID = 'price_test_ultra';

    private const COMPED_ULTRA_PRICE_ID = 'price_test_ultra_comped';

    private const PRO_PRICE_ID = 'price_test_pro';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);

        config([
            'subscriptions.plans.max.stripe_price_id' => self::ULTRA_PRICE_ID,
            'subscriptions.plans.max.stripe_price_id_comped' => self::COMPED_ULTRA_PRICE_ID,
            'subscriptions.plans.pro.stripe_price_id' => self::PRO_PRICE_ID,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createSubscriber(string $priceId, array $attributes = []): User
    {
        $user = User::factory()->create();

        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => $priceId, ...$attributes]);

        return $user;
    }

    private function createThirdPartyPlugin(?int $ultraAmount = 7000): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
        ]);

        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);

        if ($ultraAmount !== null) {
            PluginPrice::factory()->ultra()->amount($ultraAmount)->create(['plugin_id' => $plugin->id]);
        }

        return $plugin;
    }

    // ---- Who is eligible for the Ultra price tier ----

    public function test_ultra_subscriber_is_eligible_for_the_ultra_price_tier(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);

        $this->assertContains(PriceTier::Ultra, $user->getEligiblePriceTiers());
    }

    public function test_pro_subscriber_is_not_eligible_for_the_ultra_price_tier(): void
    {
        $user = $this->createSubscriber(self::PRO_PRICE_ID);

        $this->assertNotContains(PriceTier::Ultra, $user->getEligiblePriceTiers());
    }

    public function test_user_without_a_subscription_is_not_eligible_for_the_ultra_price_tier(): void
    {
        $user = User::factory()->create();

        $this->assertNotContains(PriceTier::Ultra, $user->getEligiblePriceTiers());
    }

    // ---- What each buyer pays for a third-party plugin ----

    public function test_ultra_subscriber_pays_the_ultra_price_for_a_third_party_plugin(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $plugin = $this->createThirdPartyPlugin();

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertEquals(7000, $bestPrice->amount);
        $this->assertEquals(PriceTier::Ultra, $bestPrice->tier);
    }

    public function test_comped_ultra_subscriber_pays_the_ultra_price_for_a_third_party_plugin(): void
    {
        $user = $this->createSubscriber(self::COMPED_ULTRA_PRICE_ID);
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(7000, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_ultra_subscriber_with_extra_seats_pays_the_ultra_price_for_a_third_party_plugin(): void
    {
        config(['subscriptions.plans.max.stripe_extra_seat_price_id' => 'price_test_extra_seat']);

        $user = User::factory()->create();
        $subscription = Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => null]);
        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => self::ULTRA_PRICE_ID, 'quantity' => 1]);
        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create(['stripe_price' => 'price_test_extra_seat', 'quantity' => 2]);

        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(7000, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_guest_pays_the_regular_price_for_a_third_party_plugin(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $bestPrice = $plugin->getBestPriceForUser(null);

        $this->assertEquals(9900, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    public function test_user_without_a_subscription_pays_the_regular_price_for_a_third_party_plugin(): void
    {
        $user = User::factory()->create();
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(9900, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_pro_subscriber_pays_the_regular_price_for_a_third_party_plugin(): void
    {
        $user = $this->createSubscriber(self::PRO_PRICE_ID);
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(9900, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_legacy_comped_max_subscriber_pays_the_regular_price_for_a_third_party_plugin(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID, ['is_comped' => true]);
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(9900, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_user_whose_ultra_subscription_has_ended_pays_the_regular_price_for_a_third_party_plugin(): void
    {
        $user = User::factory()->create();
        Subscription::factory()
            ->for($user)
            ->canceled()
            ->create(['stripe_price' => self::ULTRA_PRICE_ID]);
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(9900, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_team_member_without_their_own_ultra_subscription_pays_the_regular_price_for_a_third_party_plugin(): void
    {
        $owner = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(9900, $plugin->getBestPriceForUser($member)->amount);
    }

    public function test_ultra_subscriber_pays_the_regular_price_when_the_plugin_has_no_ultra_price(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $plugin = $this->createThirdPartyPlugin(ultraAmount: null);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertEquals(9900, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    public function test_ultra_subscriber_pays_the_regular_price_when_the_ultra_price_is_inactive(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $plugin = $this->createThirdPartyPlugin(ultraAmount: null);
        PluginPrice::factory()->ultra()->inactive()->amount(7000)->create(['plugin_id' => $plugin->id]);

        $this->assertEquals(9900, $plugin->getBestPriceForUser($user)->amount);
    }

    public function test_regular_price_wins_when_the_ultra_price_is_not_cheaper(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
        ]);
        PluginPrice::factory()->ultra()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertEquals(9900, $bestPrice->amount);
        $this->assertEquals(PriceTier::Regular, $bestPrice->tier);
    }

    public function test_official_plugin_never_uses_an_ultra_price(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => true,
        ]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(3100)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->ultra()->amount(2000)->create(['plugin_id' => $plugin->id]);

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertEquals(3100, $bestPrice->amount);
        $this->assertEquals(PriceTier::Subscriber, $bestPrice->tier);
    }

    // ---- The Ultra price shown to everyone else ----

    public function test_ultra_price_is_available_for_a_paid_third_party_plugin(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $this->assertEquals(7000, $plugin->getUltraPrice()->amount);
    }

    public function test_ultra_price_is_null_when_the_plugin_has_none(): void
    {
        $plugin = $this->createThirdPartyPlugin(ultraAmount: null);

        $this->assertNull($plugin->getUltraPrice());
    }

    public function test_ultra_price_is_null_when_it_is_not_lower_than_the_regular_price(): void
    {
        $plugin = $this->createThirdPartyPlugin(ultraAmount: 9900);

        $this->assertNull($plugin->getUltraPrice());
    }

    public function test_ultra_price_is_null_for_an_official_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => true]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->ultra()->amount(7000)->create(['plugin_id' => $plugin->id]);

        $this->assertNull($plugin->getUltraPrice());
    }

    public function test_ultra_price_is_null_for_a_free_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create(['is_official' => false]);
        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->ultra()->amount(7000)->create(['plugin_id' => $plugin->id]);

        $this->assertNull($plugin->getUltraPrice());
    }

    // ---- Ultra prices come from the plugin's pricing tier ----

    #[DataProvider('pricesByPluginTier')]
    public function test_setting_a_tier_on_a_third_party_plugin_creates_its_ultra_price(PluginTier $tier, int $regularAmount, int $ultraAmount): void
    {
        $plugin = Plugin::factory()->paid()->create(['is_official' => false]);

        $plugin->update(['tier' => $tier]);

        $this->assertSame($regularAmount, $plugin->prices()->forTier(PriceTier::Regular)->sole()->amount);
        $this->assertSame($ultraAmount, $plugin->prices()->forTier(PriceTier::Ultra)->sole()->amount);
    }

    /**
     * @return array<string, array{PluginTier, int, int}>
     */
    public static function pricesByPluginTier(): array
    {
        return [
            'bronze' => [PluginTier::Bronze, 2900, 2000],
            'silver' => [PluginTier::Silver, 4900, 3500],
            'gold' => [PluginTier::Gold, 9900, 7000],
        ];
    }

    public function test_changing_the_tier_updates_the_ultra_price(): void
    {
        $plugin = Plugin::factory()->paid()->create(['is_official' => false]);
        $plugin->update(['tier' => PluginTier::Gold]);

        $plugin->update(['tier' => PluginTier::Silver]);

        $this->assertSame(3500, $plugin->prices()->forTier(PriceTier::Ultra)->sole()->amount);
    }

    public function test_setting_a_tier_on_an_official_plugin_does_not_create_an_ultra_price(): void
    {
        $plugin = Plugin::factory()->paid()->create(['is_official' => true]);

        $plugin->update(['tier' => PluginTier::Gold]);

        $this->assertTrue($plugin->prices()->forTier(PriceTier::Regular)->exists());
        $this->assertFalse($plugin->prices()->forTier(PriceTier::Ultra)->exists());
    }

    // ---- The cart charges the price the buyer is entitled to ----

    public function test_cart_adds_a_third_party_plugin_at_the_ultra_price_for_an_ultra_subscriber(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $cart = Cart::factory()->for($user)->create();
        $plugin = $this->createThirdPartyPlugin();

        $item = (new CartService)->addPlugin($cart, $plugin);

        $this->assertEquals(7000, $item->price_at_addition);
        $this->assertEquals(PriceTier::Ultra, $item->pluginPrice->tier);
    }

    public function test_cart_adds_a_third_party_plugin_at_the_regular_price_for_everyone_else(): void
    {
        $user = $this->createSubscriber(self::PRO_PRICE_ID);
        $cart = Cart::factory()->for($user)->create();
        $plugin = $this->createThirdPartyPlugin();

        $item = (new CartService)->addPlugin($cart, $plugin);

        $this->assertEquals(9900, $item->price_at_addition);
        $this->assertEquals(PriceTier::Regular, $item->pluginPrice->tier);
    }

    public function test_cart_drops_to_the_ultra_price_once_the_buyer_subscribes_to_ultra(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $plugin = $this->createThirdPartyPlugin();

        $cartService = new CartService;
        $item = $cartService->addPlugin($cart, $plugin);
        $this->assertEquals(9900, $item->price_at_addition);

        Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => self::ULTRA_PRICE_ID]);

        $changes = $cartService->refreshPrices($cart->fresh());

        $this->assertCount(1, $changes);
        $this->assertEquals(9900, $changes[0]['old_price']);
        $this->assertEquals(7000, $changes[0]['new_price']);

        $item->refresh();
        $this->assertEquals(7000, $item->price_at_addition);
        $this->assertEquals(PriceTier::Ultra, $item->pluginPrice->tier);
    }

    public function test_cart_returns_to_the_regular_price_once_the_ultra_subscription_ends(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $cart = Cart::factory()->for($user)->create();
        $plugin = $this->createThirdPartyPlugin();

        $cartService = new CartService;
        $item = $cartService->addPlugin($cart, $plugin);
        $this->assertEquals(7000, $item->price_at_addition);

        $user->subscription()->update(['stripe_status' => 'canceled', 'ends_at' => now()]);

        $cartService->refreshPrices($cart->fresh());

        $item->refresh();
        $this->assertEquals(9900, $item->price_at_addition);
        $this->assertEquals(PriceTier::Regular, $item->pluginPrice->tier);
    }

    public function test_cart_page_labels_a_plugin_sold_at_the_ultra_price(): void
    {
        $user = $this->createSubscriber(self::ULTRA_PRICE_ID);
        $plugin = $this->createThirdPartyPlugin();

        $cartService = resolve(CartService::class);
        $cartService->addPlugin($cartService->getCart($user), $plugin);

        $this->actingAs($user)
            ->get(route('cart.show'))
            ->assertOk()
            ->assertSee('$70')
            ->assertSee('Ultra price');
    }

    public function test_cart_page_does_not_label_a_plugin_sold_at_the_regular_price(): void
    {
        $user = User::factory()->create();
        $plugin = $this->createThirdPartyPlugin();

        $cartService = resolve(CartService::class);
        $cartService->addPlugin($cartService->getCart($user), $plugin);

        $this->actingAs($user)
            ->get(route('cart.show'))
            ->assertOk()
            ->assertSee('$99')
            ->assertDontSee('Ultra price');
    }
}
