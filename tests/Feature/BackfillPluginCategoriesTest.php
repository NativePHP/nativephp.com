<?php

namespace Tests\Feature;

use App\Enums\PluginCategory;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillPluginCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigns_category_from_matching_keyword_in_name(): void
    {
        $plugin = Plugin::factory()->create([
            'name' => 'acme/camera-scanner',
            'repository_url' => 'https://github.com/acme/camera-scanner',
            'description' => null,
            'category' => null,
        ]);

        $this->artisan('plugins:backfill-categories')->assertSuccessful();

        $this->assertSame(PluginCategory::Media, $plugin->fresh()->category);
    }

    public function test_assigns_category_from_matching_keyword_in_description(): void
    {
        // repository_url is pinned: the factory otherwise fills it from random
        // vendor/suffix words that could match an earlier-checked category
        // (Payments is checked after Media/Security/Connectivity/Notifications)
        // and make this test flaky.
        $plugin = Plugin::factory()->create([
            'name' => 'acme/checkout-kit',
            'repository_url' => 'https://github.com/acme/checkout-kit',
            'description' => 'Accept Stripe payments in your NativePHP app.',
            'category' => null,
        ]);

        $this->artisan('plugins:backfill-categories')->assertSuccessful();

        $this->assertSame(PluginCategory::Payments, $plugin->fresh()->category);
    }

    public function test_dry_run_does_not_persist_proposed_categories(): void
    {
        $plugin = Plugin::factory()->create([
            'name' => 'acme/push-notifications',
            'category' => null,
        ]);

        $this->artisan('plugins:backfill-categories', ['--dry-run' => true])->assertSuccessful();

        $this->assertNull($plugin->fresh()->category);
    }

    public function test_already_categorized_plugins_are_left_alone(): void
    {
        $plugin = Plugin::factory()->category(PluginCategory::System)->create([
            'name' => 'acme/camera-scanner',
        ]);

        $this->artisan('plugins:backfill-categories')->assertSuccessful();

        $this->assertSame(PluginCategory::System, $plugin->fresh()->category);
    }

    public function test_plugins_with_no_confident_match_stay_uncategorized(): void
    {
        // repository_url is pinned too: the factory otherwise fills it from
        // random vendor/suffix words (e.g. "camera", "analytics") that could
        // accidentally match a keyword and make this test flaky.
        $plugin = Plugin::factory()->create([
            'name' => 'acme/utility-tool',
            'repository_url' => 'https://github.com/acme/utility-tool',
            'description' => 'A simple utility with miscellaneous helper functions for your app.',
            'category' => null,
        ]);

        $this->artisan('plugins:backfill-categories')
            ->expectsOutputToContain('No confident match')
            ->assertSuccessful();

        $this->assertNull($plugin->fresh()->category);
    }

    public function test_reports_nothing_to_do_when_no_plugins_need_backfilling(): void
    {
        Plugin::factory()->category(PluginCategory::Media)->create();

        $this->artisan('plugins:backfill-categories')
            ->expectsOutput('No plugins need a category backfilled.')
            ->assertSuccessful();
    }
}
