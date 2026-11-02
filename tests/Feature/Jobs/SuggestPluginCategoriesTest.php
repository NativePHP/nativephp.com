<?php

namespace Tests\Feature\Jobs;

use App\Enums\PluginCategory;
use App\Jobs\SuggestPluginCategories;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Boolean;
use Laravel\Ai\Prompts\ClassificationPrompt;
use RuntimeException;
use Tests\Concerns\FakesJevCategoryAnswers;
use Tests\TestCase;

class SuggestPluginCategoriesTest extends TestCase
{
    use FakesJevCategoryAnswers, RefreshDatabase;

    public function test_jev_is_asked_whether_the_plugin_belongs_in_each_category(): void
    {
        $plugin = Plugin::factory()->create([
            'display_name' => 'Barcode Scanner',
            'description' => 'Scan barcodes with the camera',
            'readme_html' => '<h1 id="barcode-scanner">Barcode Scanner<a href="#barcode-scanner" class="heading-anchor ml-2 no-underline font-medium"><span>#</span></a></h1><p>Scan barcodes &amp; QR codes.</p><ul><li>Fast</li><li>Offline</li></ul>',
        ]);

        SuggestPluginCategories::dispatchSync($plugin);

        Classification::assertClassified(function (ClassificationPrompt $prompt): bool {
            $media = $prompt->questions[PluginCategory::Media->value];

            return $prompt->state === [
                'title' => 'Barcode Scanner',
                'description' => 'Scan barcodes with the camera',
                'readme' => 'Barcode Scanner Scan barcodes & QR codes. Fast Offline',
            ]
                && array_keys($prompt->questions) === array_column(PluginCategory::cases(), 'value')
                && $media instanceof Boolean
                && $media->instructions === 'Should this plugin be listed in the Media category of the NativePHP plugin marketplace? A plugin can be listed in more than one category.'
                && $media->criteria === ['true' => 'The plugin is for cameras, photos, video, audio, barcode and QR code scanning, or speech.'];
        });
    }

    public function test_the_package_name_stands_in_for_a_missing_display_name(): void
    {
        $plugin = Plugin::factory()->withoutDescription()->create([
            'name' => 'acme/barcode-scanner',
            'display_name' => null,
            'readme_html' => null,
        ]);

        SuggestPluginCategories::dispatchSync($plugin);

        Classification::assertClassified(fn (ClassificationPrompt $prompt): bool => $prompt->state === ['title' => 'acme/barcode-scanner']);
    }

    public function test_jev_only_reads_the_start_of_a_long_readme(): void
    {
        $plugin = Plugin::factory()->create([
            'readme_html' => '<p>'.str_repeat('a', SuggestPluginCategories::README_CHARACTER_LIMIT + 1000).'</p>',
        ]);

        SuggestPluginCategories::dispatchSync($plugin);

        Classification::assertClassified(fn (ClassificationPrompt $prompt): bool => $prompt->state['readme'] === str_repeat('a', SuggestPluginCategories::README_CHARACTER_LIMIT).'...');
    }

    public function test_the_categories_jev_is_confident_about_are_suggested_likeliest_first(): void
    {
        Classification::fake([$this->categoryAnswers([
            PluginCategory::System->value => 0.7,
            PluginCategory::Media->value => 0.9,
            PluginCategory::Payments->value => 0.2,
        ])]);

        $plugin = Plugin::factory()->create();

        SuggestPluginCategories::dispatchSync($plugin);

        $plugin->refresh();

        $this->assertEquals([PluginCategory::Media->value => 0.9, PluginCategory::System->value => 0.7], $plugin->category_suggestions);
        $this->assertSame([PluginCategory::Media, PluginCategory::System], $plugin->suggestedCategories()->all());
    }

    public function test_a_category_right_on_the_threshold_is_suggested(): void
    {
        Classification::fake([$this->categoryAnswers([
            PluginCategory::Security->value => SuggestPluginCategories::THRESHOLD,
        ])]);

        $plugin = Plugin::factory()->create();

        SuggestPluginCategories::dispatchSync($plugin);

        $this->assertSame([PluginCategory::Security], $plugin->fresh()->suggestedCategories()->all());
    }

    public function test_no_more_than_three_categories_are_suggested(): void
    {
        Classification::fake([$this->categoryAnswers([
            PluginCategory::Media->value => 0.6,
            PluginCategory::Connectivity->value => 0.95,
            PluginCategory::System->value => 0.8,
            PluginCategory::Analytics->value => 0.55,
            PluginCategory::Notifications->value => 0.9,
        ])]);

        $plugin = Plugin::factory()->create();

        SuggestPluginCategories::dispatchSync($plugin);

        $this->assertSame(
            [PluginCategory::Connectivity, PluginCategory::Notifications, PluginCategory::System],
            $plugin->fresh()->suggestedCategories()->all(),
        );
    }

    public function test_nothing_is_suggested_when_jev_is_not_confident_about_any_category(): void
    {
        Classification::fake([$this->categoryAnswers([
            PluginCategory::Media->value => 0.4,
            PluginCategory::System->value => 0.3,
        ])]);

        $plugin = Plugin::factory()->create();

        SuggestPluginCategories::dispatchSync($plugin);

        $plugin->refresh();

        $this->assertSame([], $plugin->category_suggestions);
        $this->assertTrue($plugin->suggestedCategories()->isEmpty());
    }

    public function test_suggestions_come_back_likeliest_first_however_they_were_stored(): void
    {
        $plugin = Plugin::factory()->create([
            'category_suggestions' => [
                PluginCategory::System->value => 0.6,
                PluginCategory::Payments->value => 0.8,
                'retired-category' => 0.99,
            ],
        ]);

        $this->assertSame([PluginCategory::Payments, PluginCategory::System], $plugin->suggestedCategories()->all());
    }

    public function test_a_failed_classification_leaves_earlier_suggestions_alone(): void
    {
        Classification::fake(fn () => throw new RuntimeException('Jev is unavailable'));

        $plugin = Plugin::factory()->create(['category_suggestions' => [PluginCategory::Media->value => 0.9]]);

        $this->assertThrows(
            fn () => (new SuggestPluginCategories($plugin))->handle(),
            RuntimeException::class,
            'Jev is unavailable',
        );

        $this->assertEquals([PluginCategory::Media->value => 0.9], $plugin->fresh()->category_suggestions);
    }

    public function test_submitting_a_plugin_for_review_asks_jev_for_categories(): void
    {
        Queue::fake();

        $plugin = Plugin::factory()->draft()->create();

        $plugin->submit();

        Queue::assertPushed(SuggestPluginCategories::class, fn (SuggestPluginCategories $job): bool => $job->plugin->is($plugin));
    }
}
