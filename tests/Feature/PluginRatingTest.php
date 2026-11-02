<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Livewire\PluginDirectory;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginRatingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_guest_is_redirected_to_login_when_rating(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();

        $response = $this->post(route('plugins.rating.store', $plugin->routeParams()), [
            'rating' => 5,
        ]);

        $response->assertRedirect(route('customer.login'));
        $this->assertDatabaseCount('plugin_ratings', 0);
    }

    public function test_logged_in_user_can_rate_a_free_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), [
            'rating' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('plugin_ratings', [
            'plugin_id' => $plugin->id,
            'user_id' => $user->id,
            'rating' => 4,
        ]);

        $plugin->refresh();
        $this->assertEquals(4.00, $plugin->rating_average);
        $this->assertEquals(1, $plugin->rating_count);
    }

    public function test_user_cannot_rate_their_own_plugin(): void
    {
        $owner = User::factory()->create();
        $plugin = Plugin::factory()->approved()->free()->for($owner)->create();

        $response = $this->actingAs($owner)->post(route('plugins.rating.store', $plugin->routeParams()), [
            'rating' => 5,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('plugin_ratings', 0);
    }

    public function test_user_without_a_license_cannot_rate_a_paid_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), [
            'rating' => 5,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('plugin_ratings', 0);
    }

    public function test_user_with_a_license_can_rate_a_paid_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create();
        $user = User::factory()->create();

        PluginLicense::factory()->for($user)->for($plugin)->create();

        $response = $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), [
            'rating' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('plugin_ratings', [
            'plugin_id' => $plugin->id,
            'user_id' => $user->id,
            'rating' => 3,
        ]);
    }

    public function test_rating_again_updates_the_existing_rating_instead_of_creating_a_new_one(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), ['rating' => 2]);
        $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), ['rating' => 5]);

        $this->assertDatabaseCount('plugin_ratings', 1);
        $this->assertDatabaseHas('plugin_ratings', [
            'plugin_id' => $plugin->id,
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        $plugin->refresh();
        $this->assertEquals(5.00, $plugin->rating_average);
        $this->assertEquals(1, $plugin->rating_count);
    }

    public function test_rating_is_rejected_outside_the_1_to_5_range(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), [
            'rating' => 6,
        ]);

        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('plugin_ratings', 0);
    }

    public function test_user_can_remove_their_own_rating(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('plugins.rating.store', $plugin->routeParams()), ['rating' => 4]);

        $response = $this->actingAs($user)->delete(route('plugins.rating.destroy', $plugin->routeParams()));

        $response->assertRedirect();
        $this->assertDatabaseCount('plugin_ratings', 0);

        $plugin->refresh();
        $this->assertNull($plugin->rating_average);
        $this->assertEquals(0, $plugin->rating_count);
    }

    public function test_average_rating_recalculates_across_multiple_users(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();

        PluginRating::submit($plugin, User::factory()->create(), 5);
        PluginRating::submit($plugin, User::factory()->create(), 3);

        $plugin->refresh();
        $this->assertEquals(4.00, $plugin->rating_average);
        $this->assertEquals(2, $plugin->rating_count);
    }

    public function test_plugin_page_says_nothing_about_ratings_until_there_is_one(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk()
            ->assertDontSee('No ratings yet')
            ->assertDontSee('out of 5 stars');
    }

    public function test_plugin_page_shows_the_average_once_the_plugin_has_a_rating(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();

        PluginRating::submit($plugin, User::factory()->create(), 4);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk()
            ->assertSee('4.0')
            ->assertSee('(1 rating)');
    }

    public function test_plugin_card_shows_the_rating_when_the_plugin_has_one(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();

        PluginRating::submit($plugin, User::factory()->create(), 5);
        PluginRating::submit($plugin, User::factory()->create(), 4);

        Livewire::test(PluginDirectory::class)
            ->assertSee('4.5')
            ->assertSee('4.5 out of 5 stars from 2 ratings');
    }

    public function test_plugin_card_hides_the_rating_when_the_plugin_has_none(): void
    {
        Plugin::factory()->approved()->free()->create();

        Livewire::test(PluginDirectory::class)
            ->assertDontSee('out of 5 stars');
    }

    public function test_login_link_on_the_plugin_page_carries_the_plugin_page_as_the_destination(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk()
            ->assertSee(route('customer.login', [
                'redirect' => route('plugins.show', $plugin->routeParams(), false),
            ]));
    }

    public function test_user_is_returned_to_the_plugin_page_after_logging_in_to_rate_it(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();
        $user = User::factory()->create();

        $this->get(route('customer.login', [
            'redirect' => route('plugins.show', $plugin->routeParams(), false),
        ]))->assertOk();

        $this->post(route('customer.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('plugins.show', $plugin->routeParams()));
    }

    public function test_login_redirect_ignores_a_destination_outside_the_site(): void
    {
        $user = User::factory()->create();

        $this->get(route('customer.login', ['redirect' => 'https://evil.example.com/phish']))
            ->assertOk();

        $this->post(route('customer.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_login_redirect_ignores_a_protocol_relative_destination(): void
    {
        $user = User::factory()->create();

        $this->get(route('customer.login', ['redirect' => '//evil.example.com/phish']))
            ->assertOk();

        $this->post(route('customer.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }
}
