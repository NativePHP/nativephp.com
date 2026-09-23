<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A kind of plugin people have searched the marketplace for and not found.
 * Each missed search that asks for it counts as a vote.
 */
final class PluginIdea extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Start a new idea from a search that didn't match any idea we already had.
     */
    public static function startFrom(MissedPluginSearch $search): self
    {
        $idea = self::query()->create([
            'title' => Str::limit($search->term, 255, ''),
            'description' => $search->use_case,
        ]);

        $search->pluginIdea()->associate($idea)->save();

        return $idea;
    }

    /**
     * @return HasMany<MissedPluginSearch, $this>
     */
    public function missedSearches(): HasMany
    {
        return $this->hasMany(MissedPluginSearch::class);
    }
}
