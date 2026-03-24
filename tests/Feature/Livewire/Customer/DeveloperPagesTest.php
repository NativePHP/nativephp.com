<?php

namespace Tests\Feature\Livewire\Customer;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Developer\Dashboard;
use App\Livewire\Customer\Developer\Onboarding;
use App\Models\DeveloperAccount;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class DeveloperPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
    }

    // --- Onboarding ---

    public function test_onboarding_page_renders_for_user_without_developer_account(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard/developer/onboarding');

        $response->assertStatus(200);
    }

    public function test_onboarding_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/dashboard/developer/onboarding');

        $response->assertRedirect('/login');
    }

    public function test_onboarding_component_shows_start_selling_for_new_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertSee('Start Selling Plugins')
            ->assertSee('Connect with Stripe')
            ->assertSee('Plugin Developer Terms and Conditions')
            ->assertSee('Your Country')
            ->assertStatus(200);
    }

    public function test_onboarding_component_shows_continue_for_existing_account(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->pending()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertSee('Complete Your Onboarding')
            ->assertSee('Continue Onboarding')
            ->assertSee('Onboarding Incomplete')
            ->assertSee('Plugin Developer Terms and Conditions')
            ->assertStatus(200);
    }

    public function test_onboarding_component_shows_terms_accepted_for_existing_account_with_terms(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->pending()->withAcceptedTerms()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertSee('Continue Onboarding')
            ->assertSee('You accepted the')
            ->assertDontSee('I have read and agree to the')
            ->assertStatus(200);
    }

    public function test_onboarding_redirects_if_already_completed(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertRedirect(route('customer.developer.dashboard'));
    }

    public function test_onboarding_shows_benefits_section(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertSee('Why sell on NativePHP?')
            ->assertSee('70% Revenue Share')
            ->assertSee('Built-in Distribution')
            ->assertStatus(200);
    }

    public function test_onboarding_shows_faq_section(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertSee('Frequently Asked Questions')
            ->assertSee('How does the revenue share work?')
            ->assertStatus(200);
    }

    // --- Developer Dashboard ---

    public function test_developer_dashboard_renders_for_completed_account(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->create(['user_id' => $user->id]);

        $this->mock(StripeConnectService::class, function ($mock): void {
            $mock->shouldReceive('refreshAccountStatus')->once();
        });

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard/developer');

        $response->assertStatus(200);
    }

    public function test_developer_dashboard_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/dashboard/developer');

        $response->assertRedirect('/login');
    }

    public function test_developer_dashboard_redirects_without_developer_account(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertRedirect(route('customer.developer.onboarding'));
    }

    public function test_developer_dashboard_redirects_if_onboarding_incomplete(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->pending()->create(['user_id' => $user->id]);

        $this->mock(StripeConnectService::class, function ($mock): void {
            $mock->shouldReceive('refreshAccountStatus')->never();
        });

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertRedirect(route('customer.developer.onboarding'));
    }

    public function test_developer_dashboard_shows_stats_and_status(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->create(['user_id' => $user->id]);

        $this->mock(StripeConnectService::class, function ($mock): void {
            $mock->shouldReceive('refreshAccountStatus')->once();
        });

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Developer Dashboard')
            ->assertSee('Total Earnings')
            ->assertSee('Pending Payouts')
            ->assertSee('Published Plugins')
            ->assertSee('Total Sales')
            ->assertStatus(200);
    }

    public function test_developer_dashboard_shows_empty_states(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->create(['user_id' => $user->id]);

        $this->mock(StripeConnectService::class, function ($mock): void {
            $mock->shouldReceive('refreshAccountStatus')->once();
        });

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('No premium plugins yet')
            ->assertSee('No payouts yet')
            ->assertStatus(200);
    }
}
