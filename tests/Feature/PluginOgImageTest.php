<?php

namespace Tests\Feature;

use App\Jobs\GeneratePluginOgImage;
use App\Models\Plugin;
use App\Services\PluginOgLayout;
use App\Services\PluginSyncService;
use App\Services\SatisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PluginOgImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_generates_and_stores_og_image(): void
    {
        Storage::fake('public');

        $plugin = Plugin::factory()->approved()->create([
            'display_name' => 'Generated Plugin',
            'description' => 'A plugin with a generated OG image.',
            'latest_version' => '1.0.1',
            'mobile_min_version' => '^3.0',
            'ios_version' => '18.2',
            'android_version' => '26',
        ]);

        GeneratePluginOgImage::dispatchSync($plugin);

        $plugin->refresh();

        $this->assertNotNull($plugin->og_image);
        Storage::disk('public')->assertExists("og-images/plugins/{$plugin->id}.png");
    }

    public function test_sync_dispatches_og_image_generation(): void
    {
        Queue::fake();

        Http::fake([
            'api.github.com/repos/acme/test-plugin/contents/composer.json' => Http::response([
                'content' => base64_encode(json_encode(['name' => 'acme/test-plugin'])),
            ]),
            'api.github.com/repos/acme/test-plugin/contents/nativephp.json' => Http::response([], 404),
            'raw.githubusercontent.com/*' => Http::response('', 404),
            'api.github.com/repos/acme/test-plugin/releases/latest' => Http::response([], 404),
            'api.github.com/repos/acme/test-plugin/tags*' => Http::response([]),
            'api.github.com/repos/acme/test-plugin/contents/LICENSE*' => Http::response([], 404),
        ]);

        $plugin = Plugin::factory()->create([
            'name' => 'acme/test-plugin',
            'repository_url' => 'https://github.com/acme/test-plugin',
        ]);

        (new PluginSyncService)->sync($plugin);

        Queue::assertPushed(GeneratePluginOgImage::class, fn (GeneratePluginOgImage $job) => $job->plugin->is($plugin));
    }

    public function test_pills_include_all_known_plugin_details(): void
    {
        $layout = new PluginOgLayout(
            version: '2.3.1',
            mobileVersion: '^3.0.0',
            iosVersion: '15.0+',
            androidVersion: '12+',
        );

        $this->assertSame(
            ['v2.3.1', 'NativePHP Mobile ^3.0.0', 'iOS 15.0+', 'Android 12+'],
            $layout->pills(),
        );
    }

    public function test_pills_omit_unknown_plugin_details(): void
    {
        $layout = new PluginOgLayout(
            version: null,
            mobileVersion: '^3.0.0',
            iosVersion: '16.0+',
            androidVersion: null,
        );

        $this->assertSame(['NativePHP Mobile ^3.0.0', 'iOS 16.0+'], $layout->pills());
    }

    public function test_pills_are_empty_when_no_details_are_known(): void
    {
        $this->assertSame([], (new PluginOgLayout)->pills());
    }

    public function test_pills_do_not_double_the_version_prefix(): void
    {
        $this->assertSame(['v1.2.3'], (new PluginOgLayout(version: 'v1.2.3'))->pills());
    }

    public function test_pills_present_platform_versions_as_minimums(): void
    {
        $layout = new PluginOgLayout(iosVersion: '18.2', androidVersion: '26');

        $this->assertSame(['iOS 18.2+', 'Android 26+'], $layout->pills());
    }

    public function test_pills_do_not_double_the_minimum_suffix(): void
    {
        $layout = new PluginOgLayout(iosVersion: '15.0+', androidVersion: '12+');

        $this->assertSame(['iOS 15.0+', 'Android 12+'], $layout->pills());
    }

    public function test_deleting_plugin_removes_its_og_image(): void
    {
        Storage::fake('public');

        // The plugin's deleting hook also talks to Satis, which we don't exercise here.
        $this->mock(SatisService::class)
            ->shouldReceive('removePackage')
            ->andReturn([]);

        $plugin = Plugin::factory()->approved()->create([
            'og_image' => 'https://nativephp.com.test/storage/og-images/plugins/42.png',
        ]);

        Storage::disk('public')->put('og-images/plugins/42.png', 'fake-image-bytes');

        $plugin->delete();

        Storage::disk('public')->assertMissing('og-images/plugins/42.png');
    }

    public function test_backfill_command_generates_images_for_all_plugins(): void
    {
        Storage::fake('public');

        $plugins = Plugin::factory()->approved()->count(2)->create(['og_image' => null]);

        $this->artisan('plugins:generate-og-images')->assertSuccessful();

        foreach ($plugins as $plugin) {
            Storage::disk('public')->assertExists("og-images/plugins/{$plugin->id}.png");
            $this->assertNotNull($plugin->fresh()->og_image);
        }
    }

    public function test_backfill_command_missing_option_skips_existing_images(): void
    {
        Storage::fake('public');

        $existing = Plugin::factory()->approved()->create();
        Storage::disk('public')->put("og-images/plugins/{$existing->id}.png", 'original-bytes');

        $needsImage = Plugin::factory()->approved()->create(['og_image' => null]);

        $this->artisan('plugins:generate-og-images', ['--missing' => true])->assertSuccessful();

        $this->assertSame('original-bytes', Storage::disk('public')->get("og-images/plugins/{$existing->id}.png"));
        Storage::disk('public')->assertExists("og-images/plugins/{$needsImage->id}.png");
    }
}
