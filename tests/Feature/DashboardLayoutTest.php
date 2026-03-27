<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Livewire\Customer\Dashboard;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardLayoutTest extends TestCase
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
        Subscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
        ]);

        return $user;
    }

    public function test_user_name_with_apostrophe_is_not_double_escaped_in_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => "Timmy D'Hooghe",
        ]);

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('D&amp;#039;Hooghe', false);
        $response->assertSee("Timmy D'Hooghe");
    }

    // ========================================
    // Sidebar Team Item Tests
    // ========================================

    public function test_sidebar_shows_create_team_for_ultra_subscriber_without_team(): void
    {
        $user = $this->createUltraUser();

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Create Team');
    }

    public function test_sidebar_shows_team_name_for_ultra_subscriber_with_team(): void
    {
        $user = $this->createUltraUser();
        $team = Team::factory()->create(['user_id' => $user->id, 'name' => 'My Ultra Team']);

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('My Ultra Team');
    }

    public function test_sidebar_shows_team_name_for_ultra_team_member(): void
    {
        $owner = $this->createUltraUser();
        $team = Team::factory()->create(['user_id' => $owner->id, 'name' => 'Owner Team']);

        $member = User::factory()->create();
        TeamUser::factory()->active()->create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'email' => $member->email,
        ]);

        $response = $this->withoutVite()->actingAs($member)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Owner Team');
    }

    public function test_sidebar_hides_team_item_for_non_ultra_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('Create Team');
    }

    // ========================================
    // Dashboard Team Card Tests
    // ========================================

    public function test_dashboard_shows_team_card_for_ultra_subscriber_with_team(): void
    {
        $user = $this->createUltraUser();
        $team = Team::factory()->create(['user_id' => $user->id, 'name' => 'My Ultra Team']);
        TeamUser::factory()->active()->count(3)->create(['team_id' => $team->id]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertOk()
            ->assertSee('My Ultra Team')
            ->assertSee('3 active members')
            ->assertSee('Manage members');
    }

    public function test_dashboard_shows_create_team_cta_for_ultra_subscriber_without_team(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertOk()
            ->assertSee('No team yet')
            ->assertSee('Create a team');
    }

    public function test_dashboard_hides_team_card_for_non_ultra_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertOk()
            ->assertDontSee('No team yet')
            ->assertDontSee('Create a team');
    }
}
