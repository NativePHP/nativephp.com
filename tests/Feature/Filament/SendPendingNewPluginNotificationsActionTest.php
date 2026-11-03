<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Jobs\SendPendingPluginEmailDigest;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class SendPendingNewPluginNotificationsActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_action_hidden_when_no_plugins_are_pending(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertActionHidden('sendPendingNewPluginNotifications');
    }

    public function test_action_visible_when_plugins_are_pending(): void
    {
        Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertActionVisible('sendPendingNewPluginNotifications');
    }

    public function test_action_dispatches_the_digest_job(): void
    {
        Bus::fake([SendPendingPluginEmailDigest::class]);

        Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->callAction('sendPendingNewPluginNotifications')
            ->assertNotified();

        Bus::assertDispatched(SendPendingPluginEmailDigest::class);
    }
}
