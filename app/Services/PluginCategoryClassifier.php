<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PluginCategory;
use App\Models\Plugin;

final class PluginCategoryClassifier
{
    /**
     * Guess the best-fit category for a plugin from its name, description,
     * and repository URL, using the keyword map in `config('plugins.category_keywords')`.
     *
     * Returns null rather than guessing when nothing matches confidently, so
     * callers can leave the plugin uncategorized for a maintainer to review.
     */
    public function classify(Plugin $plugin): ?PluginCategory
    {
        $haystack = mb_strtolower(implode(' ', array_filter([
            $plugin->name,
            $plugin->description,
            $plugin->repository_url,
        ])));

        /** @var array<string, array<int, string>> $categoryKeywords */
        $categoryKeywords = config('plugins.category_keywords', []);

        foreach ($categoryKeywords as $categoryValue => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, mb_strtolower($keyword))) {
                    return PluginCategory::from($categoryValue);
                }
            }
        }

        return null;
    }
}
