<?php

namespace App\Models;

use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopePublished(Builder $query): void
    {
        $query
            ->orderByDesc('published_at')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Support
    |--------------------------------------------------------------------------
    */
    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->published_at && $this->published_at->isFuture();
    }

    public function publish(?DateTime $on = null)
    {
        if (! $on) {
            $on = now();
        }

        $this->update([
            'published_at' => $on,
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'published_at' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Listeners
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (auth()->check() && ! $article->author_id) {
                $article->author_id = auth()->id();
            }
        });
    }
}
