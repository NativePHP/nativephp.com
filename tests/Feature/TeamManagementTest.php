<?php

namespace Tests\Feature;

use App\Enums\PriceTier;
use App\Enums\TeamUserStatus;
use App\Features\ShowAuthButtons;
use App\Jobs\RevokeTeamUserAccessJob;
use App\Jobs\SuspendTeamJob;
use App\Jobs\UnsuspendTeamJob;
use App\Livewire\TeamManager;
use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Notifications\TeamInvitation;
use App\Notifications\TeamUserRemoved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Subscription;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_test_max_yearly';

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    private function createUltraUser(): User
    {
        $user = User::factory()->create();
        License::factory()->max()->active()->create(['user_id' => $user->id]);
        Subscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
        ]);

        return $user;
    }

    private function createTeamWithOwner(): array
    {
        $owner = $this->createUltraUser();
        $team = Team::factory()->create(['user_id' => $owner->id, 'name' => 'Test Team']);

        return [$owner, $team];
    }

    // ========================================
    // Team Creation Tests
    // ========================================

    public function test_ultra_subscriber_can_create_team(): void
    {
        $user = $this->createUltraUser();

        $response = $this->actingAs($user)
            ->post(route('customer.team.store'), ['name' => 'My Team']);

        $response->assertRedirect(route('customer.team.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teams', [
            'user_id' => $user->id,
            'name' => 'My Team',
            'is_suspended' => false,
        ]);
    }

    public function test_non_ultra_user_cannot_create_team(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('customer.team.store'), ['name' => 'My Team']);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('teams', ['user_id' => $user->id]);
    }

    public function test_cannot_create_duplicate_team(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)
            ->post(route('customer.team.store'), ['name' => 'Another Team']);

        $response->assertSessionHas('error');
        $this->assertCount(1, Team::where('user_id', $owner->id)->get());
    }

    public function test_team_name_is_required(): void
    {
        $user = $this->createUltraUser();

        $response = $this->actingAs($user)
            ->post(route('customer.team.store'), ['name' => '']);

        $response->assertSessionHasErrors('name');
    }

    // ========================================
    // Invitation Tests
    // ========================================

    public function test_owner_can_invite_member_by_email(): void
    {
        Notification::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)
            ->post(route('customer.team.invite'), ['email' => 'member@example.com']);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('team_users', [
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'status' => TeamUserStatus::Pending->value,
        ]);

        Notification::assertSentOnDemand(TeamInvitation::class);
    }

    public function test_cannot_invite_duplicate_email(): void
    {
        Notification::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'status' => TeamUserStatus::Active,
        ]);

        $response = $this->actingAs($owner)
            ->post(route('customer.team.invite'), ['email' => 'member@example.com']);

        $response->assertSessionHas('error');
    }

    public function test_can_reinvite_previously_removed_member(): void
    {
        Notification::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        // Create a removed team user (previously invited then cancelled/removed)
        $removed = TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'status' => TeamUserStatus::Removed,
            'user_id' => null,
        ]);

        $response = $this->actingAs($owner)
            ->post(route('customer.team.invite'), ['email' => 'member@example.com']);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('team_users', [
            'id' => $removed->id,
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'status' => TeamUserStatus::Pending->value,
        ]);

        // Should reuse the existing record, not create a new one
        $this->assertEquals(1, TeamUser::where('team_id', $team->id)->where('email', 'member@example.com')->count());

        Notification::assertSentOnDemand(TeamInvitation::class);
    }

    public function test_cannot_invite_when_team_suspended(): void
    {
        Notification::fake();

        $owner = $this->createUltraUser();
        $team = Team::factory()->suspended()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->post(route('customer.team.invite'), ['email' => 'member@example.com']);

        $response->assertSessionHas('error');
        Notification::assertNothingSent();
    }

    public function test_owner_cannot_invite_themselves(): void
    {
        Notification::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)
            ->post(route('customer.team.invite'), ['email' => $owner->email]);

        $response->assertSessionHas('error', 'You cannot invite yourself to your own team.');
        Notification::assertNothingSent();
        $this->assertDatabaseMissing('team_users', [
            'team_id' => $team->id,
            'email' => $owner->email,
        ]);
    }

    public function test_cannot_invite_beyond_seat_limit(): void
    {
        Notification::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        // Create 9 active members to fill all seats (owner occupies 1 of 10 included seats)
        TeamUser::factory()->count(9)->active()->create(['team_id' => $team->id]);

        $response = $this->actingAs($owner)
            ->post(route('customer.team.invite'), ['email' => 'extra@example.com']);

        $response->assertSessionHas('show_add_seats', true)
            ->assertSessionHas('error');
        Notification::assertNothingSent();
    }

    // ========================================
    // Member Removal Tests
    // ========================================

    public function test_owner_can_remove_member(): void
    {
        Notification::fake();
        Queue::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        $member = TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
        ]);

        $response = $this->actingAs($owner)
            ->delete(route('customer.team.users.remove', $member));

        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertEquals(TeamUserStatus::Removed, $member->status);

        Notification::assertSentOnDemand(TeamUserRemoved::class);
        Queue::assertPushed(RevokeTeamUserAccessJob::class);
    }

    public function test_non_owner_cannot_remove_member(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();
        $otherUser = User::factory()->create();

        $member = TeamUser::factory()->active()->create(['team_id' => $team->id]);

        $response = $this->actingAs($otherUser)
            ->delete(route('customer.team.users.remove', $member));

        $response->assertSessionHas('error');
    }

    // ========================================
    // Invitation Acceptance Tests
    // ========================================

    public function test_authenticated_user_can_accept_invitation(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create(['email' => 'member@example.com']);
        $teamUser = TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'invitation_token' => 'test-token-123',
        ]);

        $response = $this->actingAs($member)
            ->get(route('team.invitation.accept', 'test-token-123'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $teamUser->refresh();
        $this->assertEquals(TeamUserStatus::Active, $teamUser->status);
        $this->assertEquals($member->id, $teamUser->user_id);
        $this->assertNull($teamUser->invitation_token);
    }

    public function test_email_mismatch_rejects_acceptance(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $otherUser = User::factory()->create(['email' => 'other@example.com']);
        $teamUser = TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'invitation_token' => 'test-token-456',
        ]);

        $response = $this->actingAs($otherUser)
            ->get(route('team.invitation.accept', 'test-token-456'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');

        $teamUser->refresh();
        $this->assertEquals(TeamUserStatus::Pending, $teamUser->status);
    }

    public function test_unauthenticated_user_stores_token_in_session(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'newuser@example.com',
            'invitation_token' => 'test-token-789',
        ]);

        $response = $this->get(route('team.invitation.accept', 'test-token-789'));

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('pending_team_invitation_token', 'test-token-789');
    }

    public function test_invitation_accepted_after_registration(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $email = 'testuser@gmail.com';

        $teamUser = TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => $email,
            'invitation_token' => 'test-token-abc',
        ]);

        $response = $this->withSession(['pending_team_invitation_token' => 'test-token-abc'])
            ->post(route('customer.register'), [
                'name' => 'New User',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $teamUser->refresh();
        $this->assertEquals(TeamUserStatus::Active, $teamUser->status);
        $this->assertNotNull($teamUser->user_id);
    }

    public function test_invitation_accepted_after_login(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create([
            'email' => 'member@example.com',
            'password' => bcrypt('password123'),
        ]);

        $teamUser = TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'member@example.com',
            'invitation_token' => 'test-token-def',
        ]);

        $response = $this->withSession(['pending_team_invitation_token' => 'test-token-def'])
            ->post(route('customer.login'), [
                'email' => 'member@example.com',
                'password' => 'password123',
            ]);

        $teamUser->refresh();
        $this->assertEquals(TeamUserStatus::Active, $teamUser->status);
        $this->assertEquals($member->id, $teamUser->user_id);
    }

    // ========================================
    // Access Tests
    // ========================================

    public function test_team_owner_is_ultra_team_member(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $this->assertTrue($owner->isUltraTeamMember());
    }

    public function test_team_owner_gets_official_plugin_access(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $officialPlugin = Plugin::factory()->paid()->approved()->create([
            'is_official' => true,
        ]);

        $this->assertTrue($owner->hasPluginAccess($officialPlugin));
    }

    public function test_suspended_team_owner_is_not_ultra_team_member(): void
    {
        $owner = $this->createUltraUser();
        $team = Team::factory()->suspended()->create(['user_id' => $owner->id]);

        $this->assertFalse($owner->isUltraTeamMember());
    }

    public function test_team_member_gets_official_plugin_access(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $officialPlugin = Plugin::factory()->paid()->approved()->create([
            'is_official' => true,
        ]);

        $this->assertTrue($member->hasPluginAccess($officialPlugin));
    }

    public function test_team_member_does_not_get_third_party_plugin_access(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $thirdPartyPlugin = Plugin::factory()->paid()->approved()->create([
            'is_official' => false,
        ]);

        $this->assertFalse($member->hasPluginAccess($thirdPartyPlugin));
    }

    public function test_team_member_without_own_subscription_does_not_get_subscriber_pricing(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $tiers = $member->getEligiblePriceTiers();

        $this->assertNotContains(PriceTier::Subscriber, $tiers);
    }

    public function test_non_team_member_does_not_get_subscriber_pricing(): void
    {
        $user = User::factory()->create();

        $tiers = $user->getEligiblePriceTiers();

        $this->assertNotContains(PriceTier::Subscriber, $tiers);
    }

    public function test_team_member_gets_product_access_via_team(): void
    {
        $owner = $this->createUltraUser();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $product = Product::factory()->create(['slug' => 'plugin-dev-kit']);
        ProductLicense::factory()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
        ]);

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $this->assertTrue($member->hasProductLicense($product));
    }

    // ========================================
    // Suspension Tests
    // ========================================

    public function test_team_suspended_on_subscription_cancel(): void
    {
        Queue::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        $event = new WebhookReceived([
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'customer' => $owner->stripe_id ?? 'cus_test',
                ],
            ],
        ]);

        // We can't fully simulate Stripe, but we can test the job dispatch
        // by calling SuspendTeamJob directly
        $job = new SuspendTeamJob($owner->id);
        $job->handle();

        $team->refresh();
        $this->assertTrue($team->is_suspended);
    }

    public function test_team_unsuspended_on_resubscribe(): void
    {
        $owner = $this->createUltraUser();
        $team = Team::factory()->suspended()->create(['user_id' => $owner->id]);

        $job = new UnsuspendTeamJob($owner->id);
        $job->handle();

        $team->refresh();
        $this->assertFalse($team->is_suspended);
    }

    public function test_suspended_team_member_loses_access(): void
    {
        $owner = $this->createUltraUser();
        $team = Team::factory()->suspended()->create(['user_id' => $owner->id]);

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $this->assertFalse($member->isUltraTeamMember());
    }

    public function test_removed_member_loses_plugin_access(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        $teamUser = TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $officialPlugin = Plugin::factory()->paid()->approved()->create([
            'is_official' => true,
        ]);

        $this->assertTrue($member->hasPluginAccess($officialPlugin));

        $teamUser->remove();

        // Clear cached state
        $member->refresh();

        $this->assertFalse($member->hasPluginAccess($officialPlugin));
    }

    // ========================================
    // API Plugin Access Tests
    // ========================================

    public function test_api_returns_team_based_plugin_access(): void
    {
        config(['services.bifrost.api_key' => 'test-api-key']);

        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create([
            'plugin_license_key' => 'test-key-123',
        ]);
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $officialPlugin = Plugin::factory()->paid()->approved()->create([
            'is_official' => true,
            'name' => 'nativephp/test-plugin',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => 'test-api-key',
            'PHP_AUTH_USER' => $member->email,
            'PHP_AUTH_PW' => 'test-key-123',
        ])->getJson('/api/plugins/access');

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'nativephp/test-plugin',
            'access' => 'team',
        ]);
    }

    // ========================================
    // GitHub Dev Kit Access Tests
    // ========================================

    public function test_ultra_team_member_can_request_claude_plugins_access(): void
    {
        Http::fake(['github.com/*' => Http::response([], 201)]);

        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create(['github_username' => 'testuser']);
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $response = $this->actingAs($member)
            ->post(route('github.request-claude-plugins-access'));

        $response->assertSessionHas('success');
        $this->assertNotNull($member->fresh()->claude_plugins_repo_access_granted_at);
    }

    public function test_team_owner_can_request_claude_plugins_access(): void
    {
        Http::fake(['github.com/*' => Http::response([], 201)]);

        [$owner, $team] = $this->createTeamWithOwner();
        $owner->update(['github_username' => 'owneruser']);

        $response = $this->actingAs($owner)
            ->post(route('github.request-claude-plugins-access'));

        $response->assertSessionHas('success');
        $this->assertNotNull($owner->fresh()->claude_plugins_repo_access_granted_at);
    }

    public function test_non_team_member_without_product_license_cannot_request_claude_plugins_access(): void
    {
        $user = User::factory()->create(['github_username' => 'someuser']);

        $response = $this->actingAs($user)
            ->post(route('github.request-claude-plugins-access'));

        $response->assertSessionHas('error');
        $this->assertNull($user->fresh()->claude_plugins_repo_access_granted_at);
    }

    // ========================================
    // Resend Invitation Tests
    // ========================================

    public function test_owner_can_resend_invitation(): void
    {
        Notification::fake();

        [$owner, $team] = $this->createTeamWithOwner();

        $invitation = TeamUser::factory()->create([
            'team_id' => $team->id,
            'email' => 'pending@example.com',
        ]);

        $response = $this->actingAs($owner)
            ->post(route('customer.team.users.resend', $invitation));

        $response->assertSessionHas('success');
        Notification::assertSentOnDemand(TeamInvitation::class);
    }

    // ========================================
    // View Tests
    // ========================================

    public function test_team_page_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('customer.team.index'));

        $response->assertOk();
    }

    public function test_team_page_shows_create_form_for_ultra_user(): void
    {
        $user = $this->createUltraUser();

        $response = $this->actingAs($user)->get(route('customer.team.index'));

        $response->assertOk();
        $response->assertSee('Create a Team');
    }

    public function test_team_page_shows_view_plans_for_non_ultra(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('customer.team.index'));

        $response->assertOk();
        $response->assertSee('View Plans');
    }

    // ========================================
    // Team Name Update Tests
    // ========================================

    public function test_owner_can_update_team_name(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)
            ->patch(route('customer.team.update'), ['name' => 'New Team Name']);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'New Team Name',
        ]);
    }

    public function test_non_owner_cannot_update_team_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('customer.team.update'), ['name' => 'Hacked']);

        $response->assertSessionHas('error');
    }

    public function test_team_name_update_requires_name(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)
            ->patch(route('customer.team.update'), ['name' => '']);

        $response->assertSessionHasErrors('name');
    }

    public function test_team_page_shows_team_name_as_heading_for_owner(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)->get(route('customer.team.index'));

        $response->assertOk();
        $response->assertSee($team->name);
    }

    // ========================================
    // Team Detail Page Tests
    // ========================================

    public function test_team_member_can_view_team_detail_page(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $response = $this->actingAs($member)
            ->get(route('customer.team.show', $team));

        $response->assertOk();
        $response->assertSee($team->name);
        $response->assertSee('Team Membership');
    }

    public function test_non_member_cannot_view_team_detail_page(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('customer.team.show', $team));

        $response->assertForbidden();
    }

    public function test_team_owner_is_redirected_from_show_to_index(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $response = $this->actingAs($owner)
            ->get(route('customer.team.show', $team));

        $response->assertRedirect(route('customer.team.index'));
    }

    public function test_removed_member_cannot_view_team_detail_page(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
            'status' => TeamUserStatus::Removed,
        ]);

        $response = $this->actingAs($member)
            ->get(route('customer.team.show', $team));

        $response->assertForbidden();
    }

    public function test_team_detail_page_shows_official_plugins(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'nativephp/official-test',
            'is_active' => true,
            'is_official' => true,
        ]);

        $response = $this->actingAs($member)
            ->get(route('customer.team.show', $team));

        $response->assertOk();
        $response->assertSee('nativephp/official-test');
        $response->assertSee('Accessible Plugins');
    }

    public function test_team_detail_page_shows_owner_purchased_plugins(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/shared-plugin',
            'is_active' => true,
        ]);

        PluginLicense::factory()->create([
            'user_id' => $owner->id,
            'plugin_id' => $plugin->id,
        ]);

        $response = $this->actingAs($member)
            ->get(route('customer.team.show', $team));

        $response->assertOk();
        $response->assertSee('acme/shared-plugin');
        $response->assertSee('Accessible Plugins');
    }

    // ========================================
    // Seat Validation Tests
    // ========================================

    public function test_cannot_add_zero_seats(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        Livewire::actingAs($owner)
            ->test(TeamManager::class, ['team' => $team])
            ->call('addSeats', 0);

        $this->assertEquals(0, $team->fresh()->extra_seats);
    }

    public function test_cannot_add_negative_seats(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        Livewire::actingAs($owner)
            ->test(TeamManager::class, ['team' => $team])
            ->call('addSeats', -1);

        $this->assertEquals(0, $team->fresh()->extra_seats);
    }

    public function test_cannot_add_more_than_fifty_seats(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();

        Livewire::actingAs($owner)
            ->test(TeamManager::class, ['team' => $team])
            ->call('addSeats', 51);

        $this->assertEquals(0, $team->fresh()->extra_seats);
    }

    public function test_cannot_remove_zero_seats(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();
        $team->update(['extra_seats' => 5]);

        Livewire::actingAs($owner)
            ->test(TeamManager::class, ['team' => $team])
            ->call('removeSeats', 0);

        $this->assertEquals(5, $team->fresh()->extra_seats);
    }

    public function test_cannot_remove_negative_seats(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();
        $team->update(['extra_seats' => 5]);

        Livewire::actingAs($owner)
            ->test(TeamManager::class, ['team' => $team])
            ->call('removeSeats', -1);

        $this->assertEquals(5, $team->fresh()->extra_seats);
    }

    public function test_cannot_remove_more_than_fifty_seats(): void
    {
        [$owner, $team] = $this->createTeamWithOwner();
        $team->update(['extra_seats' => 60]);

        Livewire::actingAs($owner)
            ->test(TeamManager::class, ['team' => $team])
            ->call('removeSeats', 51);

        $this->assertEquals(60, $team->fresh()->extra_seats);
    }
}
