<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Models\Plugin;
use App\Models\User;
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
}
