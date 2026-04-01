<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginBundleResource\Pages\ListPluginBundles;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrantBundleToUserActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private PluginBundle $bundle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $this->bundle = PluginBundle::factory()->active()->create();
    }

    public function test_grant_to_user_action_can_be_called_with_user_id(): void
    {
        $recipient = User::factory()->create();
        $plugins = Plugin::factory()->count(2)->approved()->create();
        $this->bundle->plugins()->attach($plugins->pluck('id'));

        Livewire::actingAs($this->admin)
            ->test(ListPluginBundles::class)
            ->callAction(
                TestAction::make('grantToUser')->table($this->bundle),
                data: ['user_id' => $recipient->id],
            )
            ->assertHasNoFormErrors();

        foreach ($plugins as $plugin) {
            $this->assertDatabaseHas('plugin_licenses', [
                'user_id' => $recipient->id,
                'plugin_id' => $plugin->id,
                'plugin_bundle_id' => $this->bundle->id,
            ]);
        }
    }
}
