<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocsCoverageCategory;
use App\Enums\DocsPlatform;
use Database\Factories\DocsCoverageGapFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class DocsCoverageGap extends Model
{
    /** @use HasFactory<DocsCoverageGapFactory> */
    use HasFactory;

    protected $fillable = [
        'platform', 'category', 'identifier', 'documented', 'source_path', 'checked_at',
    ];

    #[Scope]
    protected function undocumented(Builder $query): Builder
    {
        return $query->where('documented', false);
    }

    protected function casts(): array
    {
        return [
            'platform' => DocsPlatform::class,
            'category' => DocsCoverageCategory::class,
            'documented' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }
}
