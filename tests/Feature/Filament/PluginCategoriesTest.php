<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginCategory;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Models\Plugin;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Laravel\Ai\Classification;
use Livewire\Livewire;
use RuntimeException;
use Tests\Concerns\FakesJevCategoryAnswers;
use Tests\TestCase;

class PluginCategoriesTest extends TestCase
{
    use FakesJevCategoryAnswers, RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_categories_can_be_edited_on_the_plugin_form(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->fillForm(['categories' => [PluginCategory::Media->value, PluginCategory::System->value]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([PluginCategory::Media, PluginCategory::System], $plugin->fresh()->categories->all());
    }

    public function test_jevs_suggestions_are_shown_with_how_sure_it_is(): void
    {
        $plugin = Plugin::factory()->approved()->create([
            'category_suggestions' => [PluginCategory::System->value => 0.71, PluginCategory::Media->value => 0.92],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertSee('Media (92%), System (71%)');
    }

    public function test_the_form_says_when_jev_has_not_been_asked_yet(): void
    {
        $plugin = Plugin::factory()->approved()->create(['category_suggestions' => null]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertSee('Not asked yet')
            ->assertActionHasLabel($this->action('suggestCategories'), 'Ask Jev');
    }

    public function test_asking_jev_stores_and_shows_its_suggestions(): void
    {
        Classification::fake([$this->categoryAnswers([
            PluginCategory::Media->value => 0.92,
            PluginCategory::System->value => 0.71,
        ])]);

        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction($this->action('suggestCategories'))
            ->assertNotified('Jev suggests Media and System')
            ->assertSee('Media (92%), System (71%)')
            ->assertActionHasLabel($this->action('suggestCategories'), 'Ask Jev again');

        $this->assertSame([PluginCategory::Media, PluginCategory::System], $plugin->fresh()->suggestedCategories()->all());
    }

    public function test_asking_jev_says_so_when_no_category_fits(): void
    {
        Classification::fake([$this->categoryAnswers([])]);

        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction($this->action('suggestCategories'))
            ->assertNotified('Jev isn\'t confident about any category')
            ->assertSee('No category is a confident fit')
            ->assertActionDoesNotExist($this->action('applySuggestedCategories'));
    }

    public function test_the_admin_is_told_when_jev_cannot_be_reached(): void
    {
        Exceptions::fake();
        Classification::fake(fn () => throw new RuntimeException('Jev is unavailable'));

        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction($this->action('suggestCategories'))
            ->assertNotified('Jev couldn\'t suggest categories');

        $this->assertNull($plugin->fresh()->category_suggestions);
        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_applying_suggestions_puts_the_plugin_in_exactly_those_categories(): void
    {
        $plugin = Plugin::factory()->approved()->categories(PluginCategory::Analytics)->create([
            'category_suggestions' => [PluginCategory::Notifications->value => 0.9, PluginCategory::System->value => 0.6],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction($this->action('applySuggestedCategories'))
            ->assertNotified('Categories applied')
            ->assertSchemaStateSet(['categories' => [PluginCategory::Notifications->value, PluginCategory::System->value]])
            ->assertActionDoesNotExist($this->action('applySuggestedCategories'));

        $this->assertSame([PluginCategory::Notifications, PluginCategory::System], $plugin->fresh()->categories->all());
    }

    public function test_saving_the_form_after_applying_suggestions_keeps_them(): void
    {
        $plugin = Plugin::factory()->approved()->categories(PluginCategory::Analytics)->create([
            'category_suggestions' => [PluginCategory::Notifications->value => 0.9],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction($this->action('applySuggestedCategories'))
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([PluginCategory::Notifications], $plugin->fresh()->categories->all());
    }

    public function test_apply_is_hidden_until_jev_has_suggested_something(): void
    {
        $plugin = Plugin::factory()->approved()->create(['category_suggestions' => null]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionDoesNotExist($this->action('applySuggestedCategories'));
    }

    public function test_apply_is_hidden_when_the_plugin_is_already_in_the_suggested_categories(): void
    {
        $plugin = Plugin::factory()->approved()->categories(PluginCategory::System, PluginCategory::Media)->create([
            'category_suggestions' => [PluginCategory::Media->value => 0.9, PluginCategory::System->value => 0.6],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionDoesNotExist($this->action('applySuggestedCategories'));
    }

    public function test_apply_is_offered_when_the_plugin_is_in_other_categories_too(): void
    {
        $plugin = Plugin::factory()->approved()->categories(PluginCategory::Media, PluginCategory::Payments)->create([
            'category_suggestions' => [PluginCategory::Media->value => 0.9],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible($this->action('applySuggestedCategories'));
    }

    /**
     * An action in the Categories section's header. Filament can't find one of
     * these while it's hidden, so hidden ones are asserted as not existing.
     */
    private function action(string $name): TestAction
    {
        return TestAction::make($name)->schemaComponent('categories-section');
    }
}
