<?php

namespace Tests\Concerns;

use App\Enums\PluginCategory;
use Laravel\Ai\Responses\Data\BooleanAnswer;

trait FakesJevCategoryAnswers
{
    /**
     * Jev's answer for every category, so none of them get a random fake answer.
     *
     * @param  array<string, float>  $probabilities
     * @return array<string, BooleanAnswer>
     */
    protected function categoryAnswers(array $probabilities): array
    {
        return collect(PluginCategory::cases())
            ->mapWithKeys(fn (PluginCategory $category): array => [
                $category->value => new BooleanAnswer($probabilities[$category->value] ?? 0.0),
            ])
            ->all();
    }
}
