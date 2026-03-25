<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Livewire\PluginDirectory;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class PluginDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    public function test_plugin_directory_paginates_twelve_per_page(): void
    {
        Plugin::factory()->approved()->count(13)->create();

        Livewire::test(PluginDirectory::class)
            ->assertViewHas('plugins', function ($plugins) {
                return $plugins->count() === 12
                    && $plugins->lastPage() === 2;
            });
    }
}
