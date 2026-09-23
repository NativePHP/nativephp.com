<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginEditFormTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_composer_package_name_is_not_editable(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertFormFieldDoesNotExist('name');
    }

    public function test_repository_url_is_not_editable(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertFormFieldDoesNotExist('repository_url');
    }

    public function test_submitted_at_is_not_editable(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertFormFieldDoesNotExist('created_at');
    }

    public function test_display_name_is_editable(): void
    {
        $plugin = Plugin::factory()->approved()->create(['display_name' => 'My Cool Plugin']);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertFormFieldExists('display_name');
    }

    public function test_edit_page_renders_license_type(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'composer_data' => ['license' => 'MIT'],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertSee('MIT');
    }

    public function test_edit_page_renders_package_name_as_text(): void
    {
        $plugin = Plugin::factory()->approved()->create(['name' => 'vendor/my-plugin']);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertSee('vendor/my-plugin');
    }

    public function test_approve_action_is_visible_for_pending_plugin(): void
    {
        $plugin = Plugin::factory()->pending()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('approve');
    }

    public function test_reject_action_is_visible_for_pending_plugin(): void
    {
        $plugin = Plugin::factory()->pending()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('reject');
    }

    public function test_approve_action_is_hidden_for_approved_plugin(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionHidden('approve');
    }

    public function test_status_is_shown_as_badge_in_subheading(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertSee('Approved');
    }

    public function test_status_field_is_not_in_form(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertFormFieldDoesNotExist('status');
    }

    public function test_rejection_reason_field_is_not_in_form(): void
    {
        $plugin = Plugin::factory()->rejected()->create([
            'rejection_reason' => 'Missing license',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertFormFieldDoesNotExist('rejection_reason');
    }
}
