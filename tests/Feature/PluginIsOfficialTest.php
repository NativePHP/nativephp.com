<?php

namespace Tests\Feature;

use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PluginIsOfficialTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function plugin_with_nativephp_namespace_is_automatically_marked_as_official(): void
    {
        $plugin = Plugin::factory()->create(['name' => 'nativephp/mobile-camera']);

        $this->assertTrue($plugin->fresh()->is_official);
    }

    #[Test]
    public function plugin_with_other_namespace_is_not_marked_as_official(): void
    {
        $plugin = Plugin::factory()->create(['name' => 'acme/some-plugin']);

        $this->assertFalse($plugin->fresh()->is_official);
    }

    #[Test]
    public function updating_plugin_name_to_nativephp_namespace_sets_official(): void
    {
        $plugin = Plugin::factory()->create(['name' => 'acme/some-plugin']);

        $this->assertFalse($plugin->fresh()->is_official);

        $plugin->update(['name' => 'nativephp/some-plugin']);

        $this->assertTrue($plugin->fresh()->is_official);
    }

    #[Test]
    public function updating_plugin_name_away_from_nativephp_namespace_unsets_official(): void
    {
        $plugin = Plugin::factory()->create(['name' => 'nativephp/some-plugin']);

        $this->assertTrue($plugin->fresh()->is_official);

        $plugin->update(['name' => 'acme/some-plugin']);

        $this->assertFalse($plugin->fresh()->is_official);
    }
}
