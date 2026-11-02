<?php

namespace Tests\Feature;

use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillPluginMobileVersionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_works_out_versions_from_the_stored_composer_json(): void
    {
        $plugin = Plugin::factory()->create([
            'composer_data' => ['require' => ['nativephp/mobile' => '^3.2.1 || ^4.0']],
            'mobile_versions' => null,
        ]);

        $this->artisan('plugins:backfill-mobile-versions')
            ->expectsOutputToContain('1 plugin updated.')
            ->assertSuccessful();

        $this->assertSame([3 => '3.2.1', 4 => '4.0'], $plugin->fresh()->mobile_versions);
    }

    public function test_dry_run_does_not_save_versions(): void
    {
        $plugin = Plugin::factory()->create([
            'composer_data' => ['require' => ['nativephp/mobile' => '^4.0']],
            'mobile_versions' => null,
        ]);

        $this->artisan('plugins:backfill-mobile-versions', ['--dry-run' => true])
            ->expectsOutputToContain('1 plugin would change.')
            ->assertSuccessful();

        $this->assertNull($plugin->fresh()->mobile_versions);
    }

    public function test_clears_versions_when_the_plugin_no_longer_requires_nativephp_mobile(): void
    {
        $plugin = Plugin::factory()->mobileVersions('3.0')->create([
            'composer_data' => ['require' => ['php' => '^8.2']],
        ]);

        $this->artisan('plugins:backfill-mobile-versions')->assertSuccessful();

        $this->assertNull($plugin->fresh()->mobile_versions);
    }

    public function test_plugins_that_have_never_been_synced_are_left_without_versions(): void
    {
        $plugin = Plugin::factory()->create(['composer_data' => null, 'mobile_versions' => null]);

        $this->artisan('plugins:backfill-mobile-versions')
            ->expectsOutputToContain('0 plugins updated.')
            ->assertSuccessful();

        $this->assertNull($plugin->fresh()->mobile_versions);
    }

    public function test_lists_plugins_whose_constraint_allows_none_of_the_filtered_major_versions(): void
    {
        $plugin = Plugin::factory()->create([
            'composer_data' => ['require' => ['nativephp/mobile' => '^2.0']],
        ]);

        $this->artisan('plugins:backfill-mobile-versions')
            ->expectsOutputToContain("don't allow any 4.x or 3.x release")
            ->assertSuccessful();

        $this->assertNull($plugin->fresh()->mobile_versions);
    }

    public function test_plugins_with_up_to_date_versions_are_left_alone(): void
    {
        Plugin::factory()->mobileVersions('3.0', '4.0')->create([
            'composer_data' => ['require' => ['nativephp/mobile' => '^3.0 || ^4.0']],
        ]);

        $this->artisan('plugins:backfill-mobile-versions')
            ->expectsOutputToContain('0 plugins updated.')
            ->assertSuccessful();
    }
}
