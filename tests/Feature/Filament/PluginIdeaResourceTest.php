<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginIdeaResource;
use App\Filament\Resources\PluginIdeaResource\Pages\EditPluginIdea;
use App\Filament\Resources\PluginIdeaResource\Pages\ListPluginIdeas;
use App\Filament\Resources\PluginIdeaResource\Pages\ViewPluginIdea;
use App\Filament\Resources\PluginIdeaResource\RelationManagers\MissedSearchesRelationManager;
use App\Models\MissedPluginSearch;
use App\Models\PluginIdea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginIdeaResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_list_page_shows_the_ideas_with_the_most_votes_first(): void
    {
        $nfc = PluginIdea::factory()
            ->has(MissedPluginSearch::factory(), 'missedSearches')
            ->create(['title' => 'NFC']);

        $bluetooth = PluginIdea::factory()
            ->has(MissedPluginSearch::factory()->count(3), 'missedSearches')
            ->create(['title' => 'Bluetooth']);

        Livewire::actingAs($this->admin)
            ->test(ListPluginIdeas::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$bluetooth, $nfc], inOrder: true)
            ->assertTableColumnStateSet('missed_searches_count', 3, $bluetooth)
            ->assertTableColumnStateSet('missed_searches_count', 1, $nfc);
    }

    public function test_view_page_lists_the_searches_behind_an_idea(): void
    {
        $idea = PluginIdea::factory()->create(['title' => 'Bluetooth']);

        $search = MissedPluginSearch::factory()->for($idea)->create([
            'term' => 'heart rate monitor',
            'use_case' => 'Show live heart rate from a chest strap',
            'existing_idea_probability' => 0.87,
        ]);

        $otherSearch = MissedPluginSearch::factory()->create(['term' => 'apple pay']);

        Livewire::actingAs($this->admin)
            ->test(ViewPluginIdea::class, ['record' => $idea->getRouteKey()])
            ->assertSuccessful()
            ->assertSee('Bluetooth');

        Livewire::actingAs($this->admin)
            ->test(MissedSearchesRelationManager::class, [
                'ownerRecord' => $idea,
                'pageClass' => ViewPluginIdea::class,
            ])
            ->assertCanSeeTableRecords([$search])
            ->assertCanNotSeeTableRecords([$otherSearch])
            ->assertSee('Show live heart rate from a chest strap')
            ->assertSee('87%');
    }

    public function test_an_idea_can_be_renamed(): void
    {
        $idea = PluginIdea::factory()->create(['title' => 'ble']);

        Livewire::actingAs($this->admin)
            ->test(EditPluginIdea::class, ['record' => $idea->getRouteKey()])
            ->fillForm([
                'title' => 'Bluetooth Low Energy',
                'description' => 'Scan for and talk to BLE peripherals',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $idea->refresh();

        $this->assertSame('Bluetooth Low Energy', $idea->title);
        $this->assertSame('Scan for and talk to BLE peripherals', $idea->description);
    }

    public function test_deleting_an_idea_deletes_its_searches(): void
    {
        $idea = PluginIdea::factory()
            ->has(MissedPluginSearch::factory()->count(2), 'missedSearches')
            ->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginIdeas::class)
            ->callTableAction('delete', $idea);

        $this->assertModelMissing($idea);
        $this->assertDatabaseEmpty('missed_plugin_searches');
    }

    public function test_non_admins_cannot_see_plugin_ideas(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(PluginIdeaResource::getUrl('index'))
            ->assertForbidden();
    }
}
