<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class CustomerShowcaseIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_guest_cannot_view_showcase_page(): void
    {
        $response = $this->get('/dashboard/showcase');

        $response->assertRedirect('/login');
    }

    public function test_customer_can_view_showcase_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/showcase');

        $response->assertStatus(200);
        $response->assertSee('Your Showcase Submissions');
        $response->assertSee('Submit New App');
    }

    public function test_customer_sees_empty_state_when_no_submissions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/showcase');

        $response->assertStatus(200);
        $response->assertSee('No submissions yet');
        $response->assertSee('Submit Your App');
    }

    public function test_customer_sees_their_showcase_submissions(): void
    {
        $user = User::factory()->create();

        $showcase = Showcase::factory()->approved()->create([
            'user_id' => $user->id,
            'title' => 'My Test App',
            'has_mobile' => true,
            'has_desktop' => false,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/showcase');

        $response->assertStatus(200);
        $response->assertSee('My Test App');
        $response->assertSee('Approved');
        $response->assertSee('Mobile');
    }

    public function test_customer_does_not_see_other_users_submissions(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Showcase::factory()->create([
            'user_id' => $user1->id,
            'title' => 'User 1 App',
        ]);

        Showcase::factory()->create([
            'user_id' => $user2->id,
            'title' => 'User 2 App',
        ]);

        $response = $this->actingAs($user1)->get('/dashboard/showcase');

        $response->assertStatus(200);
        $response->assertSee('User 1 App');
        $response->assertDontSee('User 2 App');
    }

    public function test_pending_showcase_shows_pending_review_status(): void
    {
        $user = User::factory()->create();

        Showcase::factory()->pending()->create([
            'user_id' => $user->id,
            'title' => 'Pending App',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/showcase');

        $response->assertStatus(200);
        $response->assertSee('Pending App');
        $response->assertSee('Pending Review');
    }

    public function test_showcase_displays_platform_badges(): void
    {
        $user = User::factory()->create();

        Showcase::factory()->both()->create([
            'user_id' => $user->id,
            'title' => 'Both Platforms App',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/showcase');

        $response->assertStatus(200);
        $response->assertSee('Mobile');
        $response->assertSee('Desktop');
    }
}
