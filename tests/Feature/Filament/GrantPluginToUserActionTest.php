<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Models\Plugin;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrantPluginToUserActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_grant_to_user_action_is_hidden_for_free_plugins_on_list(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertTableActionHidden('grantToUser', $plugin);
    }

    public function test_grant_to_user_action_is_visible_for_paid_plugins_on_list(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertTableActionVisible('grantToUser', $plugin);
    }

    public function test_grant_to_user_action_is_hidden_for_free_plugins_on_edit(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionHidden('grantToUser');
    }

    public function test_grant_to_user_action_is_visible_for_paid_plugins_on_edit(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('grantToUser');
    }

    public function test_grant_to_user_action_can_be_called_with_user_id_on_list(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();
        $recipient = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->callAction(
                TestAction::make('grantToUser')->table($plugin),
                data: ['user_id' => $recipient->id],
            )
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('plugin_licenses', [
            'user_id' => $recipient->id,
            'plugin_id' => $plugin->id,
        ]);
    }

    public function test_grant_to_user_action_can_be_called_with_user_id_on_edit(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();
        $recipient = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction('grantToUser', data: ['user_id' => $recipient->id])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('plugin_licenses', [
            'user_id' => $recipient->id,
            'plugin_id' => $plugin->id,
        ]);
    }
}
