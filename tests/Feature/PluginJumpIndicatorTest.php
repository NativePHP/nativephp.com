<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Livewire\PluginDirectory;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginJumpIndicatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_works_in_jump_defaults_to_false(): void
    {
        $plugin = Plugin::factory()->create()->refresh();

        $this->assertFalse($plugin->works_in_jump);
        $this->assertFalse($plugin->worksInJump());
    }

    public function test_works_in_jump_helper_reflects_the_column(): void
    {
        $plugin = Plugin::factory()->worksInJump()->create();

        $this->assertTrue($plugin->works_in_jump);
        $this->assertTrue($plugin->worksInJump());
    }

    public function test_detail_page_shows_pill_when_plugin_works_in_jump(): void
    {
        $plugin = Plugin::factory()->approved()->worksInJump()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('Works in Jump');
    }

    public function test_detail_page_hides_pill_when_plugin_does_not_work_in_jump(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertDontSee('Works in Jump');
    }

    public function test_plugin_card_shows_badge_when_plugin_works_in_jump(): void
    {
        Plugin::factory()->approved()->worksInJump()->create();

        Livewire::test(PluginDirectory::class)
            ->assertSee('Works in Jump');
    }

    public function test_plugin_card_hides_badge_when_plugin_does_not_work_in_jump(): void
    {
        Plugin::factory()->approved()->create();

        Livewire::test(PluginDirectory::class)
            ->assertDontSee('Works in Jump');
    }
}
