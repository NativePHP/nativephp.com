<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginListDraftFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_show_drafts_checkbox_renders_and_no_status_filter_remains(): void
    {
        Plugin::factory()->pending()->create();

        $component = Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertTableFilterExists('drafts')
            ->assertSee('Show drafts');

        $this->assertNull($component->instance()->getTable()->getFilter('status'));
    }

    public function test_draft_plugins_are_hidden_by_default(): void
    {
        $draft = Plugin::factory()->draft()->create();
        $approved = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'all')
            ->assertCanNotSeeTableRecords([$draft])
            ->assertCanSeeTableRecords([$approved]);
    }

    public function test_draft_plugins_are_visible_when_show_drafts_is_checked(): void
    {
        $draft = Plugin::factory()->draft()->create();
        $approved = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'all')
            ->filterTable('drafts', ['show_drafts' => true])
            ->assertCanSeeTableRecords([$draft, $approved]);
    }

    public function test_drafts_are_hidden_again_when_show_drafts_is_unchecked(): void
    {
        $draft = Plugin::factory()->draft()->create();
        $approved = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'all')
            ->filterTable('drafts', ['show_drafts' => true])
            ->assertCanSeeTableRecords([$draft])
            ->filterTable('drafts', ['show_drafts' => false])
            ->assertCanNotSeeTableRecords([$draft])
            ->assertCanSeeTableRecords([$approved]);
    }

    public function test_show_drafts_does_not_leak_drafts_into_the_status_tabs(): void
    {
        $draft = Plugin::factory()->draft()->create();
        $pending = Plugin::factory()->pending()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->filterTable('drafts', ['show_drafts' => true])
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$draft]);
    }
}
