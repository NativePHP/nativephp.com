<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginReportCategory;
use App\Enums\PluginReportStatus;
use App\Filament\Resources\PluginReportResource;
use App\Filament\Resources\PluginReportResource\Pages\ListPluginReports;
use App\Filament\Resources\PluginReportResource\Pages\ViewPluginReport;
use App\Models\Plugin;
use App\Models\PluginReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginReportResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_list_page_renders_successfully(): void
    {
        PluginReport::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginReports::class)
            ->assertSuccessful();
    }

    public function test_list_page_shows_plugin_and_reporter(): void
    {
        $plugin = Plugin::factory()->create(['name' => 'acme/super-plugin']);
        $reporter = User::factory()->create(['email' => 'reporter@example.com']);

        PluginReport::factory()->create([
            'plugin_id' => $plugin->id,
            'user_id' => $reporter->id,
            'category' => PluginReportCategory::MaliciousCode,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListPluginReports::class)
            ->assertSuccessful()
            ->assertSee('acme/super-plugin')
            ->assertSee('reporter@example.com');
    }

    public function test_non_admin_cannot_view_any_reports(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', PluginReport::class));
    }

    public function test_view_page_renders_successfully(): void
    {
        $report = PluginReport::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewPluginReport::class, ['record' => $report->getRouteKey()])
            ->assertSuccessful();
    }

    public function test_resolve_action_marks_the_report_resolved(): void
    {
        $report = PluginReport::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginReports::class)
            ->callTableAction('resolve', $report, data: [
                'resolution_note' => 'Investigated, plugin is fine.',
            ]);

        $report->refresh();
        $this->assertEquals(PluginReportStatus::Resolved, $report->status);
        $this->assertEquals($this->admin->id, $report->resolved_by);
        $this->assertNotNull($report->resolved_at);
        $this->assertEquals('Investigated, plugin is fine.', $report->resolution_note);
    }

    public function test_dismiss_action_marks_the_report_dismissed(): void
    {
        $report = PluginReport::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginReports::class)
            ->callTableAction('dismiss', $report, data: [
                'resolution_note' => 'Not actionable.',
            ]);

        $report->refresh();
        $this->assertEquals(PluginReportStatus::Dismissed, $report->status);
    }

    public function test_resolve_action_is_hidden_for_already_resolved_reports(): void
    {
        $report = PluginReport::factory()->resolved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginReports::class)
            ->assertTableActionHidden('resolve', $report);
    }

    public function test_navigation_badge_counts_only_open_reports(): void
    {
        PluginReport::factory()->count(2)->create();
        PluginReport::factory()->resolved()->create();

        $this->assertEquals('2', PluginReportResource::getNavigationBadge());
    }
}
