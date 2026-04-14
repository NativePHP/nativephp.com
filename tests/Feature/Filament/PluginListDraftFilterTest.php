<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginStatus;
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

    public function test_draft_plugins_are_hidden_by_default(): void
    {
        $draft = Plugin::factory()->draft()->create();
        $approved = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertCanNotSeeTableRecords([$draft])
            ->assertCanSeeTableRecords([$approved]);
    }

    public function test_draft_plugins_are_visible_when_filtering_by_draft_status(): void
    {
        $draft = Plugin::factory()->draft()->create();
        $approved = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->filterTable('status', PluginStatus::Draft->value)
            ->assertCanSeeTableRecords([$draft])
            ->assertCanNotSeeTableRecords([$approved]);
    }
}
