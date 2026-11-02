<?php

namespace Tests\Feature;

use App\Enums\PluginCategory;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Classification;
use Laravel\Ai\Prompts\ClassificationPrompt;
use RuntimeException;
use Tests\Concerns\FakesJevCategoryAnswers;
use Tests\TestCase;

class AutoCategorisePluginsTest extends TestCase
{
    use FakesJevCategoryAnswers, RefreshDatabase;

    public function test_every_plugin_is_put_in_the_categories_jev_suggests(): void
    {
        Classification::fake(fn (ClassificationPrompt $prompt): array => match ($prompt->state['title']) {
            'acme/camera' => $this->categoryAnswers([PluginCategory::Media->value => 0.9]),
            'acme/stripe' => $this->categoryAnswers([PluginCategory::Payments->value => 0.95, PluginCategory::Analytics->value => 0.6]),
        });

        $camera = Plugin::factory()->categories(PluginCategory::System)->create(['name' => 'acme/camera']);
        $stripe = Plugin::factory()->create(['name' => 'acme/stripe']);

        $this->artisan('plugins:auto-categorise')
            ->expectsTable(['Plugin', 'Categories'], [
                ['acme/camera', 'Media'],
                ['acme/stripe', 'Payments, Analytics'],
            ])
            ->expectsOutput('2 categorised, 0 left as they are, 0 failed.')
            ->assertSuccessful();

        $this->assertSame([PluginCategory::Media], $camera->fresh()->categories->all());
        $this->assertSame([PluginCategory::Payments, PluginCategory::Analytics], $stripe->fresh()->categories->all());
    }

    public function test_plugins_jev_is_not_confident_about_are_left_as_they_are(): void
    {
        Classification::fake([$this->categoryAnswers([PluginCategory::Media->value => 0.3])]);

        $plugin = Plugin::factory()->categories(PluginCategory::System)->create(['name' => 'acme/utility']);

        $this->artisan('plugins:auto-categorise')
            ->expectsOutputToContain('Jev isn\'t confident about any category')
            ->expectsOutput('0 categorised, 1 left as they are, 0 failed.')
            ->assertSuccessful();

        $plugin->refresh();

        $this->assertSame([PluginCategory::System], $plugin->categories->all());
        $this->assertSame([], $plugin->category_suggestions);
    }

    public function test_a_dry_run_asks_jev_without_changing_any_categories(): void
    {
        Classification::fake([$this->categoryAnswers([PluginCategory::Media->value => 0.9])]);

        $plugin = Plugin::factory()->categories(PluginCategory::System)->create(['name' => 'acme/camera']);

        $this->artisan('plugins:auto-categorise', ['--dry-run' => true])
            ->expectsTable(['Plugin', 'Jev Suggests'], [['acme/camera', 'Media']])
            ->expectsOutputToContain('Dry run, so no categories were changed.')
            ->assertSuccessful();

        $plugin->refresh();

        $this->assertSame([PluginCategory::System], $plugin->categories->all());
        $this->assertSame([PluginCategory::Media], $plugin->suggestedCategories()->all());
    }

    public function test_plugins_jev_already_has_suggestions_for_are_not_asked_about_again(): void
    {
        Classification::fake(fn () => throw new RuntimeException('Jev should not be asked again'));

        $plugin = Plugin::factory()->create([
            'name' => 'acme/camera',
            'category_suggestions' => [PluginCategory::Media->value => 0.9],
        ]);

        $this->artisan('plugins:auto-categorise')->assertSuccessful();

        $this->assertSame([PluginCategory::Media], $plugin->fresh()->categories->all());
    }

    public function test_a_plugin_jev_cannot_be_asked_about_is_reported_and_the_rest_carry_on(): void
    {
        Classification::fake(fn (ClassificationPrompt $prompt): array => $prompt->state['title'] === 'acme/broken'
            ? throw new RuntimeException('Jev is unavailable')
            : $this->categoryAnswers([PluginCategory::Media->value => 0.9]));

        $broken = Plugin::factory()->categories(PluginCategory::System)->create(['name' => 'acme/broken']);
        $camera = Plugin::factory()->create(['name' => 'acme/camera']);

        $this->artisan('plugins:auto-categorise')
            ->expectsTable(['Plugin', 'Error'], [['acme/broken', 'Jev is unavailable']])
            ->expectsOutput('1 categorised, 0 left as they are, 1 failed.')
            ->assertFailed();

        $broken->refresh();

        $this->assertSame([PluginCategory::System], $broken->categories->all());
        $this->assertNull($broken->category_suggestions);
        $this->assertSame([PluginCategory::Media], $camera->fresh()->categories->all());
    }

    public function test_reports_when_there_are_no_plugins(): void
    {
        $this->artisan('plugins:auto-categorise')
            ->expectsOutput('There are no plugins to categorise.')
            ->assertSuccessful();
    }
}
