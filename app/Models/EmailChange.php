<?php

namespace App\Models;

use Database\Factories\EmailChangeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class EmailChange extends Model
{
    /** @use HasFactory<EmailChangeFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('confirmed_at')->where('expires_at', '>', now());
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function confirmationUrl(): string
    {
        return URL::temporarySignedRoute('email-change.confirm', $this->expires_at, ['emailChange' => $this->id]);
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }
}
