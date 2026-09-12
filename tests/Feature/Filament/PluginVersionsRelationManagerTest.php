<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\RelationManagers\VersionsRelationManager;
use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginVersionsRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Plugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $this->plugin = Plugin::factory()->approved()->create();
    }

    public function test_it_lists_versions_for_the_plugin(): void
    {
        PluginVersion::create([
            'plugin_id' => $this->plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(VersionsRelationManager::class, [
                'ownerRecord' => $this->plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->assertSuccessful()
            ->assertCountTableRecords(1);
    }

    public function test_approve_action_clears_the_review_flag_and_advances_latest_version(): void
    {
        $version = PluginVersion::create([
            'plugin_id' => $this->plugin->id,
            'version' => '2.0.0',
            'tag_name' => 'v2.0.0',
            'github_release_id' => '2',
            'published_at' => now(),
            'permissions_expanded' => true,
            'requires_review' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(VersionsRelationManager::class, [
                'ownerRecord' => $this->plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->callTableAction('approve', $version);

        $version->refresh();
        $this->plugin->refresh();

        $this->assertFalse($version->requires_review);
        $this->assertEquals($this->admin->id, $version->approved_by);
        $this->assertNotNull($version->approved_at);
        $this->assertEquals('2.0.0', $this->plugin->latest_version);
    }

    public function test_approve_action_is_hidden_for_versions_that_do_not_require_review(): void
    {
        $version = PluginVersion::create([
            'plugin_id' => $this->plugin->id,
            'version' => '1.0.0',
            'tag_name' => 'v1.0.0',
            'github_release_id' => '1',
            'published_at' => now(),
            'requires_review' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(VersionsRelationManager::class, [
                'ownerRecord' => $this->plugin,
                'pageClass' => EditPlugin::class,
            ])
            ->assertTableActionHidden('approve', $version);
    }
}
