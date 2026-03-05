<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Features\ShowAuthButtons;
use App\Livewire\TeamManager;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    /**
     * Create a paid Max subscription for a user so they have Ultra access.
     */
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

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        return $subscription;
    }

    /**
     * Create a comped (free) Max subscription for a user — no Ultra access.
     */
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

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        return $subscription;
    }

    public function test_total_seat_capacity_with_no_extra_seats(): void
    {
        $team = Team::factory()->create(['extra_seats' => 0]);

        $this->assertEquals(Team::INCLUDED_SEATS, $team->totalSeatCapacity());
    }

    public function test_total_seat_capacity_with_extra_seats(): void
    {
        $team = Team::factory()->withExtraSeats(5)->create();

        $this->assertEquals(Team::INCLUDED_SEATS + 5, $team->totalSeatCapacity());
    }

    public function test_occupied_seat_count_includes_active_and_pending(): void
    {
        $team = Team::factory()->create();

        TeamUser::create([
            'team_id' => $team->id,
            'email' => 'active@example.com',
            'role' => 'member',
            'status' => 'active',
            'accepted_at' => now(),
        ]);

        TeamUser::create([
            'team_id' => $team->id,
            'email' => 'pending@example.com',
            'role' => 'member',
            'status' => 'pending',
            'invitation_token' => 'test-token',
            'invited_at' => now(),
        ]);

        $this->assertEquals(2, $team->occupiedSeatCount());
    }

    public function test_available_seats_calculation(): void
    {
        $team = Team::factory()->withExtraSeats(2)->create();

        // 10 included + 2 extra = 12 capacity
        $this->assertEquals(12, $team->totalSeatCapacity());

        TeamUser::create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'role' => 'member',
            'status' => 'active',
            'accepted_at' => now(),
        ]);

        $this->assertEquals(11, $team->availableSeats());
        $this->assertTrue($team->hasAvailableSeats());
    }

    public function test_has_available_seats_returns_false_when_full(): void
    {
        $team = Team::factory()->create(['extra_seats' => 0]);

        // Fill all included seats
        for ($i = 0; $i < Team::INCLUDED_SEATS; $i++) {
            TeamUser::create([
                'team_id' => $team->id,
                'email' => "member{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        $this->assertFalse($team->hasAvailableSeats());
        $this->assertEquals(0, $team->availableSeats());
    }

    public function test_can_remove_extra_seats_when_unoccupied(): void
    {
        $team = Team::factory()->withExtraSeats(3)->create();

        // Only 5 members, included seats can hold them
        for ($i = 0; $i < 5; $i++) {
            TeamUser::create([
                'team_id' => $team->id,
                'email' => "member{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        $this->assertTrue($team->canRemoveExtraSeats(3));
    }

    public function test_cannot_remove_extra_seats_when_occupied(): void
    {
        $team = Team::factory()->withExtraSeats(2)->create();

        // Fill 11 seats (1 more than included)
        for ($i = 0; $i < 11; $i++) {
            TeamUser::create([
                'team_id' => $team->id,
                'email' => "member{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        // Can't remove 2 seats because 11 members > 10 included
        $this->assertFalse($team->canRemoveExtraSeats(2));

        // Can remove 1 seat because 11 members <= 10 + 1
        $this->assertTrue($team->canRemoveExtraSeats(1));
    }

    public function test_cannot_invite_when_no_seats_available(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);
        $team = Team::factory()->create([
            'user_id' => $owner->id,
            'extra_seats' => 0,
        ]);

        // Fill all included seats
        for ($i = 0; $i < Team::INCLUDED_SEATS; $i++) {
            TeamUser::create([
                'team_id' => $team->id,
                'email' => "member{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        $response = $this->actingAs($owner)
            ->post("/customer/team/{$team->id}/members", [
                'email' => 'new@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_can_invite_beyond_ten_with_extra_seats(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);
        $team = Team::factory()->withExtraSeats(5)->create([
            'user_id' => $owner->id,
        ]);

        // Fill 10 included seats
        for ($i = 0; $i < Team::INCLUDED_SEATS; $i++) {
            TeamUser::create([
                'team_id' => $team->id,
                'email' => "member{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        $response = $this->actingAs($owner)
            ->post("/customer/team/{$team->id}/members", [
                'email' => 'eleventh@example.com',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('team_users', [
            'team_id' => $team->id,
            'email' => 'eleventh@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_pending_invitations_consume_seats(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);
        $team = Team::factory()->create([
            'user_id' => $owner->id,
            'extra_seats' => 0,
        ]);

        // Fill all but one seat with active members
        for ($i = 0; $i < Team::INCLUDED_SEATS - 1; $i++) {
            TeamUser::create([
                'team_id' => $team->id,
                'email' => "member{$i}@example.com",
                'role' => 'member',
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        // Add a pending invitation for the last seat
        TeamUser::create([
            'team_id' => $team->id,
            'email' => 'pending@example.com',
            'role' => 'member',
            'status' => 'pending',
            'invitation_token' => 'test-token',
            'invited_at' => now(),
        ]);

        // Now try to invite another - should fail
        $response = $this->actingAs($owner)
            ->post("/customer/team/{$team->id}/members", [
                'email' => 'toomany@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_cannot_invite_duplicate_email(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);
        $team = Team::factory()->create([
            'user_id' => $owner->id,
        ]);

        TeamUser::create([
            'team_id' => $team->id,
            'email' => 'existing@example.com',
            'role' => 'member',
            'status' => 'active',
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs($owner)
            ->post("/customer/team/{$team->id}/members", [
                'email' => 'existing@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_non_owner_cannot_invite_to_team(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->createPaidMaxSubscription($otherUser);
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)
            ->post("/customer/team/{$team->id}/members", [
                'email' => 'new@example.com',
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_remove_team_member(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $member = TeamUser::create([
            'team_id' => $team->id,
            'email' => 'removeme@example.com',
            'role' => 'member',
            'status' => 'active',
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs($owner)
            ->delete("/customer/team/{$team->id}/members/{$member->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('team_users', ['id' => $member->id]);
    }

    public function test_subscription_enum_is_extra_seat_price(): void
    {
        config(['subscriptions.plans.max.stripe_extra_seat_price_id' => 'price_seat_yearly']);
        config(['subscriptions.plans.max.stripe_extra_seat_price_id_monthly' => 'price_seat_monthly']);

        $this->assertTrue(Subscription::isExtraSeatPrice('price_seat_yearly'));
        $this->assertTrue(Subscription::isExtraSeatPrice('price_seat_monthly'));
        $this->assertFalse(Subscription::isExtraSeatPrice('price_some_other'));
    }

    public function test_subscription_enum_extra_seat_stripe_price_id(): void
    {
        config(['subscriptions.plans.max.stripe_extra_seat_price_id' => 'price_seat_yearly']);
        config(['subscriptions.plans.max.stripe_extra_seat_price_id_monthly' => 'price_seat_monthly']);

        $this->assertEquals('price_seat_yearly', Subscription::extraSeatStripePriceId('year'));
        $this->assertEquals('price_seat_monthly', Subscription::extraSeatStripePriceId('month'));
        $this->assertNull(Subscription::extraSeatStripePriceId('week'));
    }

    public function test_team_index_page_accessible_by_paying_team_owner(): void
    {
        $owner = User::factory()->create();
        $this->createPaidMaxSubscription($owner);
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get('/customer/team');

        $response->assertStatus(200);
        $response->assertSeeLivewire(TeamManager::class);
    }

    public function test_team_index_returns_404_for_user_without_team(): void
    {
        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);

        $response = $this->actingAs($user)->get('/customer/team');

        $response->assertStatus(404);
    }

    // ========================================
    // Ultra Access (Paid vs Comped) Tests
    // ========================================

    public function test_has_ultra_access_returns_true_for_paid_max_subscription(): void
    {
        $user = User::factory()->create();
        $this->createPaidMaxSubscription($user);

        $this->assertTrue($user->hasUltraAccess());
    }

    public function test_has_ultra_access_returns_false_for_comped_max_subscription(): void
    {
        $user = User::factory()->create();
        $this->createCompedMaxSubscription($user);

        $this->assertFalse($user->hasUltraAccess());
    }

    public function test_has_ultra_access_returns_false_for_no_subscription(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasUltraAccess());
    }

    public function test_has_ultra_access_returns_false_for_pro_subscription(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_'.uniqid()]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => Subscription::Pro->stripePriceId(),
                'is_comped' => false,
            ]);

        \Laravel\Cashier\SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Pro->stripePriceId(),
                'quantity' => 1,
            ]);

        $this->assertFalse($user->hasUltraAccess());
    }

    public function test_comped_user_cannot_access_team_page(): void
    {
        $owner = User::factory()->create();
        $this->createCompedMaxSubscription($owner);
        Team::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get('/customer/team');

        $response->assertStatus(403);
    }

    public function test_comped_user_cannot_invite_team_members(): void
    {
        $owner = User::factory()->create();
        $this->createCompedMaxSubscription($owner);
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->post("/customer/team/{$team->id}/members", [
                'email' => 'new@example.com',
            ]);

        $response->assertStatus(403);
    }

    public function test_comped_user_cannot_remove_team_members(): void
    {
        $owner = User::factory()->create();
        $this->createCompedMaxSubscription($owner);
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $member = TeamUser::create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'role' => 'member',
            'status' => 'active',
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs($owner)
            ->delete("/customer/team/{$team->id}/members/{$member->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('team_users', ['id' => $member->id]);
    }

    public function test_user_without_subscription_cannot_access_team_page(): void
    {
        $owner = User::factory()->create();
        Team::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get('/customer/team');

        $response->assertStatus(403);
    }
}
