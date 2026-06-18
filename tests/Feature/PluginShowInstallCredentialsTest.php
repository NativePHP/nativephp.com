<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PluginShowInstallCredentialsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    private function paidPlugin(): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create();
        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $plugin->id]);

        return $plugin;
    }

    public function test_install_credentials_are_masked(): void
    {
        $user = User::factory()->create(['email' => 'developer@example.test']);
        $plugin = $this->paidPlugin();

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('d••••••@example.test', false)
            ->assertSee(str_repeat('•', 16), false);
    }

    public function test_copy_command_still_contains_the_real_credentials(): void
    {
        $user = User::factory()->create(['email' => 'developer@example.test']);
        $key = $user->getPluginLicenseKey();
        $plugin = $this->paidPlugin();

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('developer@example.test', false)
            ->assertSee($key, false);
    }
}
