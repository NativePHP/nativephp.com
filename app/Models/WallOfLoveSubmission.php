<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WallOfLoveSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'photo_path',
        'url',
        'testimonial',
        'approved_at',
        'approved_by',
        'promoted',
        'promoted_testimonial',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'promoted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function isPending(): bool
    {
        return $this->approved_at === null;
    }

    public function isPromoted(): bool
    {
        return $this->promoted;
    }

    /**
     * @param  Builder<WallOfLoveSubmission>  $query
     * @return Builder<WallOfLoveSubmission>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * @param  Builder<WallOfLoveSubmission>  $query
     * @return Builder<WallOfLoveSubmission>
     */
    public function scopePromoted(Builder $query): Builder
    {
        return $query->where('promoted', true);
    }
}
