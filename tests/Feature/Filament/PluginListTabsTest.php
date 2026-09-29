<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource;
use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginListTabsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_pending_is_the_default_tab(): void
    {
        $pending = Plugin::factory()->pending()->create();
        $approved = Plugin::factory()->approved()->create();
        $rejected = Plugin::factory()->rejected()->create();
        $draft = Plugin::factory()->draft()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertSet('activeTab', 'pending')
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$approved, $rejected, $draft]);
    }

    public function test_tabs_are_ordered_pending_approved_rejected_then_all(): void
    {
        $tabs = Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->instance()
            ->getTabs();

        $this->assertSame(['pending', 'approved', 'rejected', 'all'], array_keys($tabs));
    }

    public function test_list_page_renders_the_status_tabs(): void
    {
        Plugin::factory()->pending()->create();

        $response = $this->actingAs($this->admin)->get(PluginResource::getUrl('index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Pending', 'Approved', 'Rejected', 'All']);
    }

    public function test_approved_tab_only_shows_approved_plugins(): void
    {
        $pending = Plugin::factory()->pending()->create();
        $approved = Plugin::factory()->approved()->create();
        $rejected = Plugin::factory()->rejected()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'approved')
            ->assertCanSeeTableRecords([$approved])
            ->assertCanNotSeeTableRecords([$pending, $rejected]);
    }

    public function test_approved_tab_sorts_by_approval_date_in_reverse_chronological_order(): void
    {
        $oldest = Plugin::factory()->approved()->create([
            'created_at' => now()->subDays(30),
            'approved_at' => now()->subDays(10),
        ]);
        $newest = Plugin::factory()->approved()->create([
            'created_at' => now()->subDays(2),
            'approved_at' => now()->subDay(),
        ]);
        $middle = Plugin::factory()->approved()->create([
            'created_at' => now()->subDays(20),
            'approved_at' => now()->subDays(5),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'approved')
            ->assertCanSeeTableRecords([$newest, $middle, $oldest], inOrder: true);
    }

    public function test_other_tabs_sort_by_submission_date_in_reverse_chronological_order(): void
    {
        $oldest = Plugin::factory()->pending()->create(['created_at' => now()->subDays(10)]);
        $newest = Plugin::factory()->pending()->create(['created_at' => now()->subDay()]);
        $middle = Plugin::factory()->pending()->create(['created_at' => now()->subDays(5)]);

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertCanSeeTableRecords([$newest, $middle, $oldest], inOrder: true);
    }

    public function test_approved_tab_still_honours_a_column_sort_chosen_by_the_admin(): void
    {
        $first = Plugin::factory()->approved()->create([
            'name' => 'acme/aaa-plugin',
            'approved_at' => now()->subDay(),
        ]);
        $last = Plugin::factory()->approved()->create([
            'name' => 'acme/zzz-plugin',
            'approved_at' => now()->subDays(10),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'approved')
            ->sortTable('name', 'asc')
            ->assertCanSeeTableRecords([$first, $last], inOrder: true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords([$last, $first], inOrder: true);
    }

    public function test_rejected_tab_only_shows_rejected_plugins(): void
    {
        $pending = Plugin::factory()->pending()->create();
        $approved = Plugin::factory()->approved()->create();
        $rejected = Plugin::factory()->rejected()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'rejected')
            ->assertCanSeeTableRecords([$rejected])
            ->assertCanNotSeeTableRecords([$pending, $approved]);
    }

    public function test_all_tab_shows_every_reviewable_status(): void
    {
        $pending = Plugin::factory()->pending()->create();
        $approved = Plugin::factory()->approved()->create();
        $rejected = Plugin::factory()->rejected()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->set('activeTab', 'all')
            ->assertCanSeeTableRecords([$pending, $approved, $rejected]);
    }

    public function test_hidden_columns_stay_hidden_when_switching_tabs(): void
    {
        Plugin::factory()->pending()->create();
        Plugin::factory()->approved()->create();

        $component = Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertCanRenderTableColumn('featured');

        $component
            ->call('applyTableColumnManager', $this->hideColumn($component->get('tableColumns'), 'featured'))
            ->assertCanNotRenderTableColumn('featured')
            ->set('activeTab', 'approved')
            ->assertCanNotRenderTableColumn('featured')
            ->set('activeTab', 'all')
            ->assertCanNotRenderTableColumn('featured');
    }

    public function test_hidden_columns_are_remembered_on_a_later_visit(): void
    {
        Plugin::factory()->pending()->create();

        $component = Livewire::actingAs($this->admin)->test(ListPlugins::class);

        $component->call('applyTableColumnManager', $this->hideColumn($component->get('tableColumns'), 'featured'));

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertCanNotRenderTableColumn('featured')
            ->assertCanRenderTableColumn('name');
    }

    public function test_column_customisations_can_be_reset(): void
    {
        Plugin::factory()->pending()->create();

        $component = Livewire::actingAs($this->admin)->test(ListPlugins::class);

        $component
            ->call('applyTableColumnManager', $this->hideColumn($component->get('tableColumns'), 'featured'))
            ->assertCanNotRenderTableColumn('featured')
            ->call('resetTableColumnManager')
            ->assertCanRenderTableColumn('featured');
    }

    /**
     * @param  array<int, array<string, mixed>>  $columns
     * @return array<int, array<string, mixed>>
     */
    private function hideColumn(array $columns, string $name): array
    {
        return array_map(
            fn (array $column): array => $column['name'] === $name
                ? [...$column, 'isToggled' => false]
                : $column,
            $columns,
        );
    }
}
