<?php

namespace App\Jobs;

use App\Models\MissedPluginSearch;
use App\Models\PluginIdea;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Choice;
use Laravel\Ai\Responses\Data\ChoiceAnswer;

/**
 * Asks Jev whether a missed search is for a plugin idea we've already had. If it
 * is, the search counts as a vote for that idea; if not, it starts a new one.
 */
class ClassifyMissedPluginSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const string NO_MATCH = 'none_of_the_above';

    /**
     * Jev's choice questions take up to 255 options, and one of them has to be NO_MATCH.
     */
    public const int MAX_CANDIDATES = 254;

    /**
     * How likely Jev has to think it is that we've already had the idea before the search counts as a vote.
     */
    public const float MATCH_THRESHOLD = 0.5;

    public const string INSTRUCTIONS = 'This search of the NativePHP plugin marketplace found no plugins. Which of these requested plugins is it looking for? Only pick one if building that plugin would give this search what it wants, otherwise pick '.self::NO_MATCH.'.';

    public int $maxExceptions = 3;

    public int $backoff = 60;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public MissedPluginSearch $search) {}

    /**
     * Classify one search at a time. Agents often search a few phrasings of the
     * same thing at once, and those shouldn't each start their own idea.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping('classify'))->releaseAfter(5)->expireAfter(120)];
    }

    /**
     * Waiting for another search to finish classifying shouldn't use up the job's
     * attempts, so retry by time instead. $maxExceptions still stops a job that keeps failing.
     */
    public function retryUntil(): DateTime
    {
        return now()->addHour();
    }

    public function handle(): void
    {
        if ($this->search->plugin_idea_id !== null) {
            return;
        }

        if ($idea = $this->search->ideaForSameTerm()) {
            $this->search->pluginIdea()->associate($idea)->save();

            return;
        }

        $candidates = PluginIdea::query()
            ->withCount('missedSearches')
            ->orderByDesc('missed_searches_count')
            ->latest('id')
            ->limit(self::MAX_CANDIDATES)
            ->get()
            ->keyBy(fn (PluginIdea $idea): string => "idea_{$idea->id}");

        if ($candidates->isEmpty()) {
            PluginIdea::startFrom($this->search);

            return;
        }

        /** @var ChoiceAnswer $answer */
        $answer = Classification::of($this->state())
            ->question('idea', new Choice(self::INSTRUCTIONS, $this->options($candidates)))
            ->classify()
            ->answer('idea');

        $ideaProbabilities = collect($answer->probabilities)->only($candidates->keys());

        $this->search->existing_idea_probability = round($ideaProbabilities->sum(), 4);

        if ($this->search->existing_idea_probability < self::MATCH_THRESHOLD) {
            PluginIdea::startFrom($this->search);

            return;
        }

        $this->search->pluginIdea()
            ->associate($candidates->get($ideaProbabilities->sortDesc()->keys()->first()))
            ->save();
    }

    /**
     * @return array<string, string>
     */
    protected function state(): array
    {
        return array_filter([
            'search' => $this->search->term,
            'use_case' => $this->search->use_case,
        ]);
    }

    /**
     * @param  Collection<string, PluginIdea>  $candidates
     * @return array<string, string>
     */
    protected function options(Collection $candidates): array
    {
        return $candidates
            ->map(fn (PluginIdea $idea): string => Str::limit(
                $idea->description ? "{$idea->title}: {$idea->description}" : $idea->title,
                200,
            ))
            ->put(self::NO_MATCH, 'A plugin that none of the other options describe')
            ->all();
    }
}
