<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Showcase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'screenshots',
        'has_mobile',
        'has_desktop',
        'play_store_url',
        'app_store_url',
        'windows_download_url',
        'macos_download_url',
        'linux_download_url',
        'certified_nativephp',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'screenshots' => 'array',
        'has_mobile' => 'boolean',
        'has_desktop' => 'boolean',
        'certified_nativephp' => 'boolean',
        'approved_at' => 'datetime',
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

    public function isNew(): bool
    {
        return $this->approved_at !== null && $this->approved_at->isAfter(now()->subMonth());
    }

    public function needsReReview(): bool
    {
        return $this->approved_at !== null && $this->updated_at->isAfter($this->approved_at);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('approved_at');
    }

    public function scopeWithMobile(Builder $query): Builder
    {
        return $query->where('has_mobile', true);
    }

    public function scopeWithDesktop(Builder $query): Builder
    {
        return $query->where('has_desktop', true);
    }
}
