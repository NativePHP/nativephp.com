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

    public function test_install_commands_are_shown(): void
    {
        $plugin = $this->paidPlugin();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('php artisan vendor:publish --tag=nativephp-plugins-provider')
            ->assertSee('composer require '.$plugin->name)
            ->assertSee('php artisan native:plugin:register '.$plugin->name);
    }

    public function test_credentials_section_is_collapsible_and_still_renders_credentials(): void
    {
        $user = User::factory()->create(['email' => 'developer@example.test']);
        $key = $user->getPluginLicenseKey();
        $plugin = $this->paidPlugin();

        $this->actingAs($user)
            ->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Configure Composer')
            ->assertSee($key, false);
    }

    public function test_install_box_links_to_the_using_plugins_guide(): void
    {
        $plugin = $this->paidPlugin();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee(url('docs/mobile/3/plugins/using-plugins'), false)
            ->assertSee('Using Plugins guide');
    }
}
