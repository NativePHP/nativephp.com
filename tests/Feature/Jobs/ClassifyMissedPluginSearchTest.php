<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ClassifyMissedPluginSearch;
use App\Models\MissedPluginSearch;
use App\Models\PluginIdea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Choice;
use Laravel\Ai\Prompts\ClassificationPrompt;
use Laravel\Ai\Responses\Data\ChoiceAnswer;
use RuntimeException;
use Tests\TestCase;

class ClassifyMissedPluginSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_first_missed_search_starts_an_idea_without_asking_jev(): void
    {
        Classification::fake();

        $search = MissedPluginSearch::factory()->unclassified()->create([
            'term' => 'bluetooth',
            'use_case' => 'Read heart rate from a BLE chest strap',
        ]);

        ClassifyMissedPluginSearch::dispatchSync($search);

        $idea = PluginIdea::sole();
        $search->refresh();

        $this->assertSame('bluetooth', $idea->title);
        $this->assertSame('Read heart rate from a BLE chest strap', $idea->description);
        $this->assertTrue($search->pluginIdea->is($idea));
        $this->assertNull($search->existing_idea_probability);

        Classification::assertNothingClassified();
    }

    public function test_a_repeat_of_an_earlier_search_term_counts_towards_the_same_idea_without_asking_jev(): void
    {
        Classification::fake();

        $idea = PluginIdea::factory()->create();
        MissedPluginSearch::factory()->for($idea)->create(['term' => 'Bluetooth']);

        $search = MissedPluginSearch::factory()->unclassified()->create(['term' => 'bluetooth']);

        ClassifyMissedPluginSearch::dispatchSync($search);

        $this->assertTrue($search->fresh()->pluginIdea->is($idea));
        $this->assertSame(1, PluginIdea::count());

        Classification::assertNothingClassified();
    }

    public function test_jev_is_asked_which_existing_idea_the_search_is_for(): void
    {
        $bluetooth = PluginIdea::factory()->create(['title' => 'Bluetooth', 'description' => 'Talk to BLE devices']);
        MissedPluginSearch::factory()->for($bluetooth)->create(['term' => 'ble']);
        $nfc = PluginIdea::factory()->create(['title' => 'NFC', 'description' => null]);

        Classification::fake([
            ['idea' => $this->answer([
                "idea_{$bluetooth->id}" => 0.1,
                "idea_{$nfc->id}" => 0.1,
                ClassifyMissedPluginSearch::NO_MATCH => 0.8,
            ])],
        ]);

        $search = MissedPluginSearch::factory()->unclassified()->create([
            'term' => 'heart rate monitor',
            'use_case' => 'Show live heart rate from a chest strap',
        ]);

        ClassifyMissedPluginSearch::dispatchSync($search);

        Classification::assertClassified(function (ClassificationPrompt $prompt) use ($bluetooth, $nfc): bool {
            $question = $prompt->questions['idea'];

            return $prompt->state === ['search' => 'heart rate monitor', 'use_case' => 'Show live heart rate from a chest strap']
                && $question instanceof Choice
                && $question->instructions === ClassifyMissedPluginSearch::INSTRUCTIONS
                && $question->options === [
                    "idea_{$bluetooth->id}" => 'Bluetooth: Talk to BLE devices',
                    "idea_{$nfc->id}" => 'NFC',
                    ClassifyMissedPluginSearch::NO_MATCH => 'A plugin that none of the other options describe',
                ];
        });
    }

    public function test_a_search_jev_matches_to_an_existing_idea_counts_as_a_vote_for_it(): void
    {
        $bluetooth = PluginIdea::factory()->create(['title' => 'Bluetooth']);
        $nfc = PluginIdea::factory()->create(['title' => 'NFC']);

        Classification::fake([
            ['idea' => $this->answer([
                "idea_{$bluetooth->id}" => 0.8,
                "idea_{$nfc->id}" => 0.05,
                ClassifyMissedPluginSearch::NO_MATCH => 0.15,
            ])],
        ]);

        $search = MissedPluginSearch::factory()->unclassified()->create(['term' => 'heart rate monitor']);

        ClassifyMissedPluginSearch::dispatchSync($search);

        $search->refresh();

        $this->assertTrue($search->pluginIdea->is($bluetooth));
        $this->assertSame(0.85, $search->existing_idea_probability);
        $this->assertSame(2, PluginIdea::count());
    }

    public function test_a_search_jev_does_not_match_starts_a_new_idea(): void
    {
        $bluetooth = PluginIdea::factory()->create(['title' => 'Bluetooth']);

        Classification::fake([
            ['idea' => $this->answer([
                "idea_{$bluetooth->id}" => 0.1,
                ClassifyMissedPluginSearch::NO_MATCH => 0.9,
            ])],
        ]);

        $search = MissedPluginSearch::factory()->unclassified()->create([
            'term' => 'apple pay',
            'use_case' => 'Take payments in a food truck app',
        ]);

        ClassifyMissedPluginSearch::dispatchSync($search);

        $idea = $search->fresh()->pluginIdea;

        $this->assertFalse($idea->is($bluetooth));
        $this->assertSame('apple pay', $idea->title);
        $this->assertSame('Take payments in a food truck app', $idea->description);
        $this->assertSame(0.1, $search->fresh()->existing_idea_probability);
    }

    public function test_likelihood_spread_across_similar_ideas_counts_as_a_vote_for_the_likeliest(): void
    {
        $bluetooth = PluginIdea::factory()->create(['title' => 'Bluetooth']);
        $ble = PluginIdea::factory()->create(['title' => 'BLE']);

        Classification::fake([
            ['idea' => $this->answer([
                "idea_{$bluetooth->id}" => 0.3,
                "idea_{$ble->id}" => 0.35,
                ClassifyMissedPluginSearch::NO_MATCH => 0.35,
            ])],
        ]);

        $search = MissedPluginSearch::factory()->unclassified()->create(['term' => 'bluetooth low energy']);

        ClassifyMissedPluginSearch::dispatchSync($search);

        $search->refresh();

        $this->assertTrue($search->pluginIdea->is($ble));
        $this->assertSame(0.65, $search->existing_idea_probability);
        $this->assertSame(2, PluginIdea::count());
    }

    public function test_a_search_starts_a_new_idea_when_jev_gives_no_probabilities(): void
    {
        $bluetooth = PluginIdea::factory()->create(['title' => 'Bluetooth']);

        Classification::fake([
            ['idea' => new ChoiceAnswer("idea_{$bluetooth->id}", [])],
        ]);

        $search = MissedPluginSearch::factory()->unclassified()->create(['term' => 'nfc']);

        ClassifyMissedPluginSearch::dispatchSync($search);

        $this->assertSame('nfc', $search->fresh()->pluginIdea->title);
        $this->assertSame(2, PluginIdea::count());
    }

    public function test_the_ideas_with_the_most_votes_are_offered_when_there_are_too_many_to_list(): void
    {
        Classification::fake();

        $popular = PluginIdea::factory()->create();
        MissedPluginSearch::factory()->for($popular)->create(['term' => 'bluetooth']);
        $leastRecent = PluginIdea::factory()->create();
        PluginIdea::factory()->count(ClassifyMissedPluginSearch::MAX_CANDIDATES - 1)->create();

        $search = MissedPluginSearch::factory()->unclassified()->create(['term' => 'nfc']);

        ClassifyMissedPluginSearch::dispatchSync($search);

        Classification::assertClassified(function (ClassificationPrompt $prompt) use ($popular, $leastRecent): bool {
            $options = $prompt->questions['idea']->options;

            return count($options) === ClassifyMissedPluginSearch::MAX_CANDIDATES + 1
                && array_key_first($options) === "idea_{$popular->id}"
                && ! array_key_exists("idea_{$leastRecent->id}", $options);
        });
    }

    public function test_a_search_that_already_counts_towards_an_idea_is_left_alone(): void
    {
        Classification::fake();

        $idea = PluginIdea::factory()->create();
        $search = MissedPluginSearch::factory()->for($idea)->create();

        ClassifyMissedPluginSearch::dispatchSync($search);

        $this->assertTrue($search->fresh()->pluginIdea->is($idea));
        $this->assertSame(1, PluginIdea::count());

        Classification::assertNothingClassified();
    }

    public function test_a_failed_classification_leaves_the_search_to_be_retried(): void
    {
        PluginIdea::factory()->create();

        Classification::fake(fn () => throw new RuntimeException('Jev is unavailable'));

        $search = MissedPluginSearch::factory()->unclassified()->create();

        $this->assertThrows(
            fn () => (new ClassifyMissedPluginSearch($search))->handle(),
            RuntimeException::class,
            'Jev is unavailable',
        );

        $this->assertNull($search->fresh()->plugin_idea_id);
        $this->assertSame(1, PluginIdea::count());
    }

    /**
     * @param  array<string, float>  $probabilities
     */
    private function answer(array $probabilities): ChoiceAnswer
    {
        return new ChoiceAnswer(
            array_search(max($probabilities), $probabilities, true),
            $probabilities,
            max($probabilities),
        );
    }
}
