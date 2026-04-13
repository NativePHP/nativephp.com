<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\UserResource;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_free_plugin_shows_packagist_link_for_composer_name(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create([
            'name' => 'acme/camera-123',
        ]);

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $response->assertSee('https://packagist.org/packages/acme/camera-123');
    }

    public function test_paid_plugin_does_not_show_packagist_link(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create([
            'name' => 'acme/paid-plugin-123',
        ]);

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $response->assertDontSee('https://packagist.org/packages/acme/paid-plugin-123');
    }

    public function test_paid_third_party_plugin_hides_repository_url_placeholder(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create([
            'name' => 'acme/paid-plugin-456',
            'repository_url' => 'https://github.com/acme/paid-plugin-456',
            'is_official' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertDontSee('Repository URL');
    }

    public function test_paid_official_plugin_shows_repository_url(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create([
            'name' => 'nativephp/paid-official-789',
            'repository_url' => 'https://github.com/nativephp/paid-official-789',
            'is_official' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $response->assertSee('https://github.com/nativephp/paid-official-789');
    }

    public function test_free_plugin_shows_repository_url(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create([
            'name' => 'acme/free-plugin-321',
            'repository_url' => 'https://github.com/acme/free-plugin-321',
            'is_official' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $response->assertSee('https://github.com/acme/free-plugin-321');
    }

    public function test_paid_plugin_license_links_to_license_page(): void
    {
        $plugin = Plugin::factory()->paid()->approved()->create([
            'name' => 'acme/paid-license-111',
            'composer_data' => ['license' => 'Commercial'],
        ]);

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $response->assertSee(route('plugins.license', ['vendor' => 'acme', 'package' => 'paid-license-111']));
    }

    public function test_free_plugin_license_links_to_github(): void
    {
        $plugin = Plugin::factory()->free()->approved()->create([
            'name' => 'acme/free-license-222',
            'repository_url' => 'https://github.com/acme/free-license-222',
            'composer_data' => ['license' => 'MIT'],
        ]);

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $response->assertSee('https://github.com/acme/free-license-222/blob/main/LICENSE');
    }

    public function test_submission_info_shows_go_to_user_action(): void
    {
        $user = User::factory()->create();
        $plugin = Plugin::factory()->for($user)->approved()->create();

        $response = $this->actingAs($this->admin)->get(
            PluginResource::getUrl('edit', ['record' => $plugin])
        );

        $response->assertOk();
        $expectedUrl = UserResource::getUrl('edit', ['record' => $user->id]);
        $response->assertSee($expectedUrl);
    }
}
