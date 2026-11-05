<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DocsFeedbackFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class DocsFeedback extends Model
{
    /** @use HasFactory<DocsFeedbackFactory> */
    use HasFactory;

    protected $fillable = [
        'platform', 'version', 'page', 'helpful', 'comment', 'ip_hash',
    ];

    #[Scope]
    protected function helpful(Builder $query): Builder
    {
        return $query->where('helpful', true);
    }

    #[Scope]
    protected function unhelpful(Builder $query): Builder
    {
        return $query->where('helpful', false);
    }

    protected function casts(): array
    {
        return [
            'helpful' => 'boolean',
        ];
    }
}
