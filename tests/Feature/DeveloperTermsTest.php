<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Livewire\Customer\Developer\Onboarding;
use App\Livewire\Customer\Plugins\Create;
use App\Models\DeveloperAccount;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class DeveloperTermsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
        Feature::define(ShowPlugins::class, true);
    }

    /** @test */
    public function developer_terms_page_is_accessible(): void
    {
        $response = $this->get('/developer-terms');

        $response->assertStatus(200);
        $response->assertSee('Plugin Developer Terms and Conditions');
    }

    /** @test */
    public function developer_terms_page_contains_key_sections(): void
    {
        $response = $this->get('/developer-terms');

        $response->assertSee('Revenue Share and Platform Fee');
        $response->assertSee('thirty percent (30%)');
        $response->assertSee('Developer Responsibilities and Liability');
        $response->assertSee('Listing Criteria and Marketplace Standards');
        $response->assertSee('Plugin Pricing and Discounts');
    }

    /** @test */
    public function onboarding_start_requires_terms_acceptance(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('customer.developer.onboarding.start'));

        $response->assertSessionHasErrors('accepted_plugin_terms');
    }

    /** @test */
    public function onboarding_start_rejects_unchecked_terms(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('customer.developer.onboarding.start'), [
                'accepted_plugin_terms' => '0',
            ]);

        $response->assertSessionHasErrors('accepted_plugin_terms');
    }

    /** @test */
    public function onboarding_start_records_terms_acceptance(): void
    {
        $user = User::factory()->create();

        $mockService = Mockery::mock(StripeConnectService::class);
        $mockService->shouldReceive('createConnectAccount')
            ->once()
            ->andReturnUsing(fn () => DeveloperAccount::factory()->pending()->create(['user_id' => $user->id]));
        $mockService->shouldReceive('createOnboardingLink')
            ->once()
            ->andReturn('https://connect.stripe.com/setup/test');

        $this->app->instance(StripeConnectService::class, $mockService);

        $response = $this->actingAs($user)
            ->post(route('customer.developer.onboarding.start'), [
                'accepted_plugin_terms' => '1',
            ]);

        $response->assertRedirect('https://connect.stripe.com/setup/test');

        $developerAccount = $user->fresh()->developerAccount;
        $this->assertNotNull($developerAccount->accepted_plugin_terms_at);
        $this->assertEquals(
            DeveloperAccount::CURRENT_PLUGIN_TERMS_VERSION,
            $developerAccount->plugin_terms_version
        );
    }

    /** @test */
    public function onboarding_start_does_not_overwrite_existing_terms_acceptance(): void
    {
        $user = User::factory()->create();
        $originalTime = now()->subDays(30);

        $developerAccount = DeveloperAccount::factory()->pending()->create([
            'user_id' => $user->id,
            'accepted_plugin_terms_at' => $originalTime,
            'plugin_terms_version' => DeveloperAccount::CURRENT_PLUGIN_TERMS_VERSION,
        ]);

        $mockService = Mockery::mock(StripeConnectService::class);
        $mockService->shouldReceive('createOnboardingLink')
            ->once()
            ->andReturn('https://connect.stripe.com/setup/test');

        $this->app->instance(StripeConnectService::class, $mockService);

        $this->actingAs($user)
            ->post(route('customer.developer.onboarding.start'), [
                'accepted_plugin_terms' => '1',
            ]);

        $developerAccount->refresh();
        $this->assertEquals(
            $originalTime->toDateTimeString(),
            $developerAccount->accepted_plugin_terms_at->toDateTimeString()
        );
    }

    /** @test */
    public function developer_account_has_accepted_plugin_terms_returns_true_when_accepted(): void
    {
        $account = DeveloperAccount::factory()->withAcceptedTerms()->create();

        $this->assertTrue($account->hasAcceptedPluginTerms());
    }

    /** @test */
    public function developer_account_has_accepted_plugin_terms_returns_false_when_not_accepted(): void
    {
        $account = DeveloperAccount::factory()->create();

        $this->assertFalse($account->hasAcceptedPluginTerms());
    }

    /** @test */
    public function developer_account_has_accepted_current_terms_returns_false_for_old_version(): void
    {
        $account = DeveloperAccount::factory()->withAcceptedTerms('0.9')->create();

        $this->assertTrue($account->hasAcceptedPluginTerms());
        $this->assertFalse($account->hasAcceptedCurrentTerms());
    }

    /** @test */
    public function developer_account_has_accepted_current_terms_returns_true_for_current_version(): void
    {
        $account = DeveloperAccount::factory()->withAcceptedTerms()->create();

        $this->assertTrue($account->hasAcceptedCurrentTerms());
    }

    /** @test */
    public function terms_of_service_mentions_third_party_plugins(): void
    {
        $response = $this->get('/terms-of-service');

        $response->assertStatus(200);
        $response->assertSee('Third-Party Plugins');
    }

    /** @test */
    public function privacy_policy_mentions_third_party_plugin_purchases(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
        $response->assertSee('Third-Party Plugin Purchases');
    }

    /** @test */
    public function onboarding_page_renders_for_new_developer(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertStatus(200)
            ->assertSee('Become a Plugin Developer')
            ->assertSee('Start Selling Plugins');
    }

    /** @test */
    public function onboarding_page_redirects_when_fully_onboarded(): void
    {
        $user = User::factory()->create();
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertRedirect(route('customer.developer.dashboard'));
    }

    /** @test */
    public function plugin_create_page_renders_for_github_connected_user(): void
    {
        $user = User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'testdev',
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertStatus(200)
            ->assertSee('Submit Your Plugin')
            ->assertSee('Select Repository');
    }

    /** @test */
    public function plugin_create_page_shows_github_required_for_non_connected_user(): void
    {
        $user = User::factory()->create([
            'github_id' => null,
            'github_username' => null,
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertStatus(200)
            ->assertSee('GitHub Connection Required');
    }
}
