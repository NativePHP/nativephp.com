<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Jobs\SyncPluginReleases;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class ConvertPluginToPaidTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_convert_to_paid_changes_type_and_dispatches_satis(): void
    {
        Bus::fake([SyncPluginReleases::class]);

        $plugin = Plugin::factory()->free()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->callTableAction('convertToPaid', $plugin, data: [
                'tier' => PluginTier::Silver->value,
            ])
            ->assertNotified();

        $plugin->refresh();

        $this->assertEquals(PluginType::Paid, $plugin->type);
        $this->assertEquals(PluginTier::Silver, $plugin->tier);
        $this->assertTrue($plugin->prices()->exists());

        Bus::assertDispatched(SyncPluginReleases::class, function ($job) use ($plugin) {
            return $job->plugin->is($plugin);
        });
    }

    public function test_convert_to_paid_is_not_visible_on_paid_plugins(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertTableActionHidden('convertToPaid', $plugin);
    }

    public function test_convert_to_paid_is_visible_on_free_plugins(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPlugins::class)
            ->assertTableActionVisible('convertToPaid', $plugin);
    }
}
