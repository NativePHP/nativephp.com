<?php

namespace Tests\Feature;

use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Enums\Subscription;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPrice;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\SubscriptionItem;
use Tests\TestCase;

class UltraPluginAccessTest extends TestCase
{
    use RefreshDatabase;

    private const COMPED_ULTRA_PRICE_ID = 'price_test_ultra_comped';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id_comped' => self::COMPED_ULTRA_PRICE_ID]);
    }

    private function createCompedUltraSubscription(User $user): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => self::COMPED_ULTRA_PRICE_ID,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => self::COMPED_ULTRA_PRICE_ID,
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createPaidMaxSubscription(User $user): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'is_comped' => false,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createCompedMaxSubscription(User $user): \Laravel\Cashier\Subscription
    {
        $user->update(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'is_comped' => true,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        return $subscription;
    }

    private function createOfficialPlugin(): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/test-plugin',
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(1999)->create(['plugin_id' => $plugin->id]);

        return $plugin;
    }

    private function createThirdPartyPlugin(): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'vendor/third-party-plugin',
            'is_active' => true,
            'is_official' => false,
        ]);

        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->subscriber()->amount(2900)->create(['plugin_id' => $plugin->id]);

        return $plugin;
    }

    // ---- Phase 1: Free official plugins for Ultra ----

    public function test_ultra_user_gets_zero_price_for_official_plugin(): void
    {
        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);
        $plugin = $this->createOfficialPlugin();

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(0, $bestPrice->amount);
    }

    public function test_ultra_user_gets_normal_subscriber_price_for_third_party_plugin(): void
    {
        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);
        $plugin = $this->createThirdPartyPlugin();

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2900, $bestPrice->amount);
    }

    public function test_comped_ultra_user_does_not_get_free_official_plugin(): void
    {
        $user = User::factory()->create();
        $this->createCompedMaxSubscription($user);
        $plugin = $this->createOfficialPlugin();

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertGreaterThan(0, $bestPrice->amount);
    }

    public function test_non_subscriber_gets_regular_price_for_official_plugin(): void
    {
        $user = User::factory()->create();
        $plugin = $this->createOfficialPlugin();

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2999, $bestPrice->amount);
    }

    public function test_guest_gets_regular_price_for_official_plugin(): void
    {
        $plugin = $this->createOfficialPlugin();

        $bestPrice = $plugin->getBestPriceForUser(null);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2999, $bestPrice->amount);
    }

    // ---- Phase 2: $0 cart checkout ----

    public function test_zero_cart_skips_stripe_and_creates_licenses(): void
    {
        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);
        $plugin = $this->createOfficialPlugin();

        $this->actingAs($user);

        $cartService = resolve(CartService::class);
        $cart = $cartService->getCart($user);
        $cartService->addPlugin($cart, $plugin);

        // Verify the price was captured as $0
        $cartItem = $cart->items()->first();
        $this->assertEquals(0, $cartItem->price_at_addition);

        // Checkout should skip Stripe and redirect to free success
        $response = $this->post(route('cart.checkout'));
        $response->assertRedirect(route('cart.success', ['free' => 1]));

        // License should have been created
        $this->assertTrue(
            $user->pluginLicenses()->forPlugin($plugin)->active()->exists()
        );

        // Cart should be completed
        $this->assertNotNull($cart->fresh()->completed_at);
    }

    public function test_free_checkout_does_not_create_duplicate_licenses(): void
    {
        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);
        $plugin = $this->createOfficialPlugin();

        // Pre-existing license
        PluginLicense::factory()->create([
            'user_id' => $user->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $this->actingAs($user);

        $cartService = resolve(CartService::class);
        $cart = $cartService->getCart($user);
        $cartService->addPlugin($cart, $plugin);

        $this->post(route('cart.checkout'));

        // Should still only have 1 license
        $this->assertEquals(1, $user->pluginLicenses()->forPlugin($plugin)->count());
    }

    public function test_free_checkout_success_page_renders(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('cart.success', ['free' => 1]));
        $response->assertStatus(200);
        $response->assertSee('Plugins Added!');
    }

    // ---- Phase 3: Team plugin access ----

    public function test_team_member_has_plugin_access_via_owner_license(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/licensed-plugin',
            'type' => PluginType::Paid,
            'is_active' => true,
            'is_official' => true,
        ]);

        $this->assertTrue($member->hasPluginAccess($plugin));
    }

    public function test_team_member_loses_access_when_removed(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        $teamUser = TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/licensed-plugin',
            'type' => PluginType::Paid,
            'is_active' => true,
            'is_official' => true,
        ]);

        $this->assertTrue($member->hasPluginAccess($plugin));

        // Remove from team
        $teamUser->delete();

        $this->assertFalse($member->hasPluginAccess($plugin));
    }

    public function test_non_team_member_does_not_get_team_plugin_access(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        Team::factory()->create(['user_id' => $owner->id]);
        $nonMember = User::factory()->create();

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/licensed-plugin',
            'type' => PluginType::Paid,
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $this->assertFalse($nonMember->hasPluginAccess($plugin));
    }

    public function test_pending_team_member_does_not_get_plugin_access(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'pending',
            'role' => 'member',
            'invited_at' => now(),
        ]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/licensed-plugin',
            'type' => PluginType::Paid,
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $this->assertFalse($member->hasPluginAccess($plugin));
    }

    public function test_satis_api_includes_team_plugins(): void
    {
        $owner = User::factory()->create([
            'plugin_license_key' => 'owner-key',
        ]);
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create([
            'plugin_license_key' => 'member-key',
        ]);

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'nativephp/team-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => config('services.bifrost.api_key'),
            'Authorization' => 'Basic '.base64_encode("{$member->email}:member-key"),
        ])->getJson('/api/plugins/access');

        $response->assertStatus(200);

        $plugins = $response->json('plugins');
        $pluginNames = array_column($plugins, 'name');

        $this->assertContains('nativephp/team-plugin', $pluginNames);

        $teamPlugin = collect($plugins)->firstWhere('name', 'nativephp/team-plugin');
        $this->assertEquals('team', $teamPlugin['access']);
    }

    public function test_satis_check_access_returns_true_for_team_member(): void
    {
        $owner = User::factory()->create([
            'plugin_license_key' => 'owner-key',
        ]);
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create([
            'plugin_license_key' => 'member-key',
        ]);

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'nativephp/team-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => config('services.bifrost.api_key'),
            'Authorization' => 'Basic '.base64_encode("{$member->email}:member-key"),
        ])->getJson('/api/plugins/access/nativephp/team-plugin');

        $response->assertStatus(200)
            ->assertJson([
                'has_access' => true,
            ]);
    }

    // ---- Phase 4: Team Plugins on Purchased Plugins page ----

    public function test_purchased_plugins_page_shows_team_plugins_for_team_member(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/shared-plugin',
            'is_active' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $this->actingAs($member);
        $response = $this->get(route('customer.purchased-plugins.index'));

        $response->assertStatus(200);
        $response->assertSee('Team Plugins');
        $response->assertSee($owner->display_name);
        $response->assertSee('nativephp/shared-plugin');
    }

    public function test_purchased_plugins_page_does_not_show_team_plugins_for_non_member(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('customer.purchased-plugins.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Team Plugins');
    }

    // ---- Phase 5: Team suspension (cancelled/defaulted subscription) ----

    public function test_team_member_has_access_to_owner_purchased_third_party_plugin(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = $this->createThirdPartyPlugin();

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        $this->assertTrue($member->hasPluginAccess($plugin));
    }

    public function test_team_member_loses_plugin_access_when_team_suspended(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $officialPlugin = $this->createOfficialPlugin();
        $thirdPartyPlugin = $this->createThirdPartyPlugin();

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $thirdPartyPlugin->id,
            'expires_at' => null,
        ]);

        // Before suspension, member has access
        $this->assertTrue($member->hasPluginAccess($officialPlugin));
        $this->assertTrue($member->hasPluginAccess($thirdPartyPlugin));

        // Suspend the team
        $team->suspend();

        // After suspension, member loses team-granted access
        $this->assertFalse($member->hasPluginAccess($officialPlugin));
        $this->assertFalse($member->hasPluginAccess($thirdPartyPlugin));
    }

    public function test_team_member_keeps_own_license_when_team_suspended(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = $this->createThirdPartyPlugin();

        // Member bought this plugin themselves
        PluginLicense::factory()->create([
            'user_id' => $member->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        // Suspend the team
        $team->suspend();

        // Member keeps access via their own license
        $this->assertTrue($member->hasPluginAccess($plugin));
    }

    public function test_satis_api_excludes_team_plugins_when_team_suspended(): void
    {
        $owner = User::factory()->create([
            'plugin_license_key' => 'owner-key',
        ]);
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create([
            'plugin_license_key' => 'member-key',
        ]);

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'nativephp/team-plugin',
            'type' => PluginType::Paid,
            'status' => PluginStatus::Approved,
            'is_active' => true,
            'is_official' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
            'expires_at' => null,
        ]);

        // Suspend the team
        $team->suspend();

        $response = $this->withHeaders([
            'X-API-Key' => config('services.bifrost.api_key'),
            'Authorization' => 'Basic '.base64_encode("{$member->email}:member-key"),
        ])->getJson('/api/plugins/access');

        $response->assertStatus(200);

        $pluginNames = array_column($response->json('plugins'), 'name');
        $this->assertNotContains('nativephp/team-plugin', $pluginNames);
    }

    // ---- Phase 6: Team member pricing ----

    public function test_team_member_without_own_subscription_sees_regular_price(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = $this->createThirdPartyPlugin();

        $bestPrice = $plugin->getBestPriceForUser($member);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(4900, $bestPrice->amount);
    }

    public function test_team_member_with_own_subscription_sees_subscriber_price(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);

        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();
        $this->createPaidMaxSubscription($member);

        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => 'active',
            'role' => 'member',
            'accepted_at' => now(),
        ]);

        $plugin = $this->createThirdPartyPlugin();

        $bestPrice = $plugin->getBestPriceForUser($member);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(2900, $bestPrice->amount);
    }

    // ---- Comped Ultra subscriptions ----

    public function test_comped_ultra_user_has_active_ultra_subscription(): void
    {
        $user = User::factory()->create();
        $this->createCompedUltraSubscription($user);

        $this->assertTrue($user->hasActiveUltraSubscription());
    }

    public function test_comped_ultra_user_has_ultra_access(): void
    {
        $user = User::factory()->create();
        $this->createCompedUltraSubscription($user);

        $this->assertTrue($user->hasUltraAccess());
    }

    public function test_comped_ultra_user_gets_free_official_plugin(): void
    {
        $user = User::factory()->create();
        $this->createCompedUltraSubscription($user);
        $plugin = $this->createOfficialPlugin();

        $bestPrice = $plugin->getBestPriceForUser($user);

        $this->assertNotNull($bestPrice);
        $this->assertEquals(0, $bestPrice->amount);
    }

    public function test_legacy_comped_max_does_not_have_active_ultra_subscription(): void
    {
        $user = User::factory()->create();
        $this->createCompedMaxSubscription($user);

        $this->assertFalse($user->hasActiveUltraSubscription());
    }

    public function test_legacy_comped_max_does_not_have_ultra_access(): void
    {
        $user = User::factory()->create();
        $this->createCompedMaxSubscription($user);

        $this->assertFalse($user->hasUltraAccess());
    }
}
