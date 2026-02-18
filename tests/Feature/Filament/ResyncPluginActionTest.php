<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Jobs\SyncPlugin;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class ResyncPluginActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_resync_action_dispatches_job(): void
    {
        Bus::fake([SyncPlugin::class]);

        $plugin = Plugin::factory()->free()->approved()->create([
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction('resync')
            ->assertNotified();

        Bus::assertDispatched(SyncPlugin::class, function ($job) use ($plugin) {
            return $job->plugin->is($plugin);
        });
    }

    public function test_resync_action_visible_when_repository_url_exists(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create([
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('resync');
    }

    public function test_resync_action_hidden_when_no_repository_url(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create([
            'repository_url' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionHidden('resync');
    }
}