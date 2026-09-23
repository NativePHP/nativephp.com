<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\RelationManagers\RatingsRelationManager;
use App\Models\Plugin;
use App\Models\PluginRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginRatingsRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Plugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $this->plugin = Plugin::factory()->approved()->free()->create();
    }

    public function test_it_lists_ratings_for_the_plugin(): void
    {
        PluginRating::submit($this->plugin, User::factory()->create(['email' => 'rater@example.com']), 5);

        Livewire::actingAs($this->admin)
            ->test(RatingsRelationManager::class, [
                'ownerRecord' => $this->plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->assertSuccessful()
            ->assertSee('rater@example.com')
            ->assertCountTableRecords(1);
    }

    public function test_admin_can_delete_an_abusive_rating_and_the_average_recalculates(): void
    {
        PluginRating::submit($this->plugin, User::factory()->create(), 5);
        $rating = PluginRating::submit($this->plugin, User::factory()->create(), 1);

        $this->plugin->refresh();
        $this->assertEquals(3.00, $this->plugin->rating_average);

        Livewire::actingAs($this->admin)
            ->test(RatingsRelationManager::class, [
                'ownerRecord' => $this->plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->callTableAction('delete', $rating);

        $this->assertDatabaseMissing('plugin_ratings', ['id' => $rating->id]);

        $this->plugin->refresh();
        $this->assertEquals(5.00, $this->plugin->rating_average);
        $this->assertEquals(1, $this->plugin->rating_count);
    }

    public function test_it_shows_zero_ratings_for_a_plugin_with_none(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RatingsRelationManager::class, [
                'ownerRecord' => $this->plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->assertCountTableRecords(0);
    }
}
