<?php

namespace App\Jobs;

use App\Enums\PluginCategory;
use App\Models\Plugin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Boolean;
use Laravel\Ai\Contracts\Question;
use Laravel\Ai\Responses\Data\Answer;
use Laravel\Ai\Responses\Data\BooleanAnswer;

/**
 * Asks Jev which marketplace categories a plugin belongs in, and keeps the ones
 * it's confident about as suggestions for an admin to apply.
 */
class SuggestPluginCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * How likely Jev has to think it is that a plugin belongs in a category before it's suggested.
     */
    public const float THRESHOLD = 0.5;

    public const int MAX_SUGGESTIONS = 3;

    /**
     * The start of a README says what the plugin is for. The rest is mostly
     * installation steps and API reference, so Jev only reads this much of it.
     */
    public const int README_CHARACTER_LIMIT = 5000;

    public const string INSTRUCTIONS = 'Should this plugin be listed in the %s category of the NativePHP plugin marketplace? A plugin can be listed in more than one category.';

    public int $tries = 3;

    public int $backoff = 60;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public Plugin $plugin) {}

    public function handle(): void
    {
        $answers = Classification::of($this->state())
            ->questions($this->questions())
            ->classify()
            ->collect();

        $this->plugin->update([
            'category_suggestions' => $answers
                ->only(array_column(PluginCategory::cases(), 'value'))
                ->filter(fn (Answer $answer): bool => $answer instanceof BooleanAnswer && $answer->isTrue(self::THRESHOLD))
                ->map(fn (BooleanAnswer $answer): float => round($answer->probability, 4))
                ->sortDesc()
                ->take(self::MAX_SUGGESTIONS)
                ->all(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function state(): array
    {
        return array_filter([
            'title' => $this->plugin->display_name ?? $this->plugin->name,
            'description' => $this->plugin->description,
            'readme' => $this->readme(),
        ]);
    }

    /**
     * The README as plain text, without the "#" link the site adds to every
     * heading. Tags are spaced out before they're stripped so that text from
     * neighbouring elements doesn't run together.
     */
    protected function readme(): ?string
    {
        if (blank($this->plugin->readme_html)) {
            return null;
        }

        $html = preg_replace('/<a\b[^>]*\bheading-anchor\b[^>]*>.*?<\/a>/s', '', $this->plugin->readme_html);

        $text = html_entity_decode(strip_tags(str_replace('<', ' <', $html)), ENT_QUOTES | ENT_HTML5);

        return Str::limit(Str::squish($text), self::README_CHARACTER_LIMIT);
    }

    /**
     * One yes/no question per category, so a plugin that fits several isn't
     * forced to split Jev's confidence between them.
     *
     * @return array<string, Question>
     */
    protected function questions(): array
    {
        return collect(PluginCategory::cases())
            ->mapWithKeys(fn (PluginCategory $category): array => [
                $category->value => new Boolean(
                    sprintf(self::INSTRUCTIONS, $category->label()),
                    ['true' => "The plugin is for {$category->description()}."],
                ),
            ])
            ->all();
    }
}
