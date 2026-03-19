<?php

namespace Tests\Feature\Livewire\Customer;

use App\Features\ShowAuthButtons;
use App\Livewire\Customer\Integrations;
use App\Livewire\Customer\Showcase\Create as ShowcaseCreate;
use App\Livewire\Customer\Showcase\Edit as ShowcaseEdit;
use App\Livewire\Customer\WallOfLove\Create as WallOfLoveCreate;
use App\Models\License;
use App\Models\Showcase;
use App\Models\User;
use App\Models\WallOfLoveSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class Step1PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    // --- Integrations ---

    public function test_integrations_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/customer/integrations');

        $response->assertStatus(200);
    }

    public function test_integrations_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/customer/integrations');

        $response->assertRedirect('/login');
    }

    public function test_integrations_component_renders_heading(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Integrations::class)
            ->assertSee('Integrations')
            ->assertSee('Connect your accounts')
            ->assertStatus(200);
    }

    // --- Showcase Create ---

    public function test_showcase_create_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/customer/showcase/create');

        $response->assertStatus(200);
    }

    public function test_showcase_create_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/customer/showcase/create');

        $response->assertRedirect('/login');
    }

    public function test_showcase_create_component_shows_guidelines(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ShowcaseCreate::class)
            ->assertSee('Submit Your App to the Showcase')
            ->assertSee('Showcase Guidelines')
            ->assertStatus(200);
    }

    // --- Showcase Edit ---

    public function test_showcase_edit_page_renders_for_owner(): void
    {
        $user = User::factory()->create();
        $showcase = Showcase::factory()->create(['user_id' => $user->id]);

        $response = $this->withoutVite()->actingAs($user)->get("/customer/showcase/{$showcase->id}/edit");

        $response->assertStatus(200);
    }

    public function test_showcase_edit_page_returns_403_for_non_owner(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $showcase = Showcase::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withoutVite()->actingAs($user)->get("/customer/showcase/{$showcase->id}/edit");

        $response->assertStatus(403);
    }

    public function test_showcase_edit_page_requires_authentication(): void
    {
        $showcase = Showcase::factory()->create();

        $response = $this->withoutVite()->get("/customer/showcase/{$showcase->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_showcase_edit_component_shows_status_badge(): void
    {
        $user = User::factory()->create();
        $showcase = Showcase::factory()->approved()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(ShowcaseEdit::class, ['showcase' => $showcase])
            ->assertSee('Edit Your Submission')
            ->assertSee('Approved')
            ->assertStatus(200);
    }

    // --- Wall of Love Create ---

    public function test_wall_of_love_page_renders_for_eligible_user(): void
    {
        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2025-01-15',
        ]);

        $response = $this->withoutVite()->actingAs($user)->get('/customer/wall-of-love/create');

        $response->assertStatus(200);
    }

    public function test_wall_of_love_page_returns_404_for_ineligible_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withoutVite()->actingAs($user)->get('/customer/wall-of-love/create');

        $response->assertStatus(404);
    }

    public function test_wall_of_love_page_redirects_if_already_submitted(): void
    {
        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2025-01-15',
        ]);
        WallOfLoveSubmission::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(WallOfLoveCreate::class)
            ->assertRedirect(route('dashboard'));
    }

    public function test_wall_of_love_page_requires_authentication(): void
    {
        $response = $this->withoutVite()->get('/customer/wall-of-love/create');

        $response->assertRedirect('/login');
    }
}
