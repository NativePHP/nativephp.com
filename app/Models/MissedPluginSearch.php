<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PluginType;
use App\Jobs\ClassifyMissedPluginSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A search of the public plugin marketplace MCP tool that found nothing.
 */
final class MissedPluginSearch extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Store the search and queue it up for Jev to work out which plugin idea it's a vote for.
     */
    public static function record(string $term, ?PluginType $type = null, ?string $useCase = null): self
    {
        $search = self::query()->create([
            'term' => Str::limit(Str::squish($term), 500, ''),
            'type' => $type,
            'use_case' => filled($useCase) ? Str::limit(trim($useCase), 1000, '') : null,
        ]);

        ClassifyMissedPluginSearch::dispatch($search);

        return $search;
    }

    /**
     * Find the idea that searches for the same term already count towards.
     */
    public function ideaForSameTerm(): ?PluginIdea
    {
        return PluginIdea::query()
            ->whereHas('missedSearches', fn (Builder $query) => $query
                ->whereRaw('LOWER(term) = ?', [Str::lower($this->term)]))
            ->latest('id')
            ->first();
    }

    /**
     * @return BelongsTo<PluginIdea, $this>
     */
    public function pluginIdea(): BelongsTo
    {
        return $this->belongsTo(PluginIdea::class);
    }

    protected function casts(): array
    {
        return [
            'type' => PluginType::class,
            'existing_idea_probability' => 'float',
        ];
    }
}
