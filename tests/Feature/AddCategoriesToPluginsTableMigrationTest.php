<?php

namespace Tests\Feature;

use App\Enums\PluginCategory;
use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCategoriesToPluginsTableMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_plugins_existing_category_becomes_its_only_category(): void
    {
        $media = Plugin::factory()->create(['category' => PluginCategory::Media->value]);
        $payments = Plugin::factory()->create(['category' => PluginCategory::Payments->value]);
        $uncategorized = Plugin::factory()->create(['category' => null]);

        $migration = $this->migration();
        $migration->down();
        $migration->up();

        $this->assertSame([PluginCategory::Media], $media->fresh()->categories->all());
        $this->assertSame([PluginCategory::Payments], $payments->fresh()->categories->all());
        $this->assertNull($uncategorized->fresh()->categories);
        $this->assertNull($media->fresh()->category_suggestions);
    }

    private function migration(): Migration
    {
        return require database_path('migrations/2026_09_25_171044_add_categories_to_plugins_table.php');
    }
}
