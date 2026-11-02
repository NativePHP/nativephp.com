<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Livewire\Customer\Plugins\Show;
use App\Livewire\PluginDirectory;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginIconFallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    private function createUserWithGitHub(): User
    {
        $user = User::factory()->create([
            'github_id' => '12345',
            'github_token' => encrypt('fake-token'),
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }

    public function test_it_recognises_real_heroicon_outline_names(): void
    {
        $this->assertTrue(Plugin::isValidIconName('cube'));
        $this->assertTrue(Plugin::isValidIconName('photo'));
        $this->assertTrue(Plugin::isValidIconName('map-pin'));
    }

    public function test_it_rejects_names_that_are_not_heroicons(): void
    {
        // The names from the reported 500s: neither exists in Heroicons.
        $this->assertFalse(Plugin::isValidIconName('image'));
        $this->assertFalse(Plugin::isValidIconName('location'));

        $this->assertFalse(Plugin::isValidIconName(null));
        $this->assertFalse(Plugin::isValidIconName(''));
        $this->assertFalse(Plugin::isValidIconName('Cube'));
        $this->assertFalse(Plugin::isValidIconName('../../secret'));
    }

    public function test_icon_component_uses_the_stored_name_when_it_is_valid(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'icon_gradient' => 'blue-cyan',
            'icon_name' => 'map-pin',
        ]);

        $this->assertSame('heroicon-o-map-pin', $plugin->getIconComponent());
    }

    public function test_icon_component_falls_back_when_the_stored_name_is_unknown(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'icon_gradient' => 'blue-cyan',
            'icon_name' => 'location',
        ]);

        $this->assertSame('heroicon-o-cube', $plugin->getIconComponent());
    }

    public function test_developer_plugin_page_renders_with_an_unknown_stored_icon(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->approved()->for($user)->create([
            'icon_gradient' => 'blue-cyan',
            'icon_name' => 'location',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->assertStatus(200);
    }

    public function test_plugin_directory_renders_with_an_unknown_stored_icon(): void
    {
        Plugin::factory()->approved()->create([
            'icon_gradient' => 'blue-cyan',
            'icon_name' => 'image',
        ]);

        Livewire::test(PluginDirectory::class)
            ->assertStatus(200);
    }

    public function test_public_plugin_listing_renders_with_an_unknown_stored_icon(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'icon_gradient' => 'blue-cyan',
            'icon_name' => 'image',
        ]);

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertOk();
    }

    public function test_updating_the_icon_rejects_a_name_that_is_not_a_heroicon(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'icon_gradient' => null,
            'icon_name' => null,
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('iconGradient', 'blue-cyan')
            ->set('iconName', 'location')
            ->call('updateIcon')
            ->assertHasErrors('iconName');

        $plugin->refresh();
        $this->assertNull($plugin->icon_name);
        $this->assertNull($plugin->icon_gradient);
    }

    public function test_updating_the_icon_accepts_a_real_heroicon(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'icon_gradient' => null,
            'icon_name' => null,
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('iconGradient', 'blue-cyan')
            ->set('iconName', 'map-pin')
            ->call('updateIcon')
            ->assertHasNoErrors();

        $plugin->refresh();
        $this->assertSame('map-pin', $plugin->icon_name);
        $this->assertSame('blue-cyan', $plugin->icon_gradient);
    }
}
