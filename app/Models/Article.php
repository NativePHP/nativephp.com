<?php

namespace App\Models;

use App\Services\ArticleImageService;
use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
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
        'og_image',
        'hero_image',
        'card_image',
        'header_image',
        'og_image_crop',
        'card_image_crop',
        'header_image_crop',
        'content',
        'published_at',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getHeroImageUrl(): ?string
    {
        if (! $this->hero_image) {
            return null;
        }

        return asset('storage/'.$this->hero_image);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query
            ->latest('published_at')
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

        static::creating(function ($article): void {
            if (auth()->check() && ! $article->author_id) {
                $article->author_id = auth()->id();
            }
        });

        static::updated(function (Article $article): void {
            resolve(ArticleImageService::class)->pruneStaleImages($article);
        });

        static::deleting(function (Article $article): void {
            resolve(ArticleImageService::class)->deleteImages($article);
        });
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'og_image_crop' => 'array',
            'card_image_crop' => 'array',
            'header_image_crop' => 'array',
        ];
    }
}
