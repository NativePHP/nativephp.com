<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'is_suspended',
    ];

    /**
     * @return BelongsTo<User>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<TeamUser>
     */
    public function users(): HasMany
    {
        return $this->hasMany(TeamUser::class);
    }

    /**
     * @return HasMany<TeamUser>
     */
    public function activeUsers(): HasMany
    {
        return $this->hasMany(TeamUser::class)
            ->where('status', \App\Enums\TeamUserStatus::Active);
    }

    /**
     * @return HasMany<TeamUser>
     */
    public function pendingInvitations(): HasMany
    {
        return $this->hasMany(TeamUser::class)
            ->where('status', \App\Enums\TeamUserStatus::Pending);
    }

    public function activeUserCount(): int
    {
        return $this->activeUsers()->count();
    }

    public function isOverIncludedLimit(): bool
    {
        return $this->activeUserCount() >= 10;
    }

    public function extraSeatsCount(): int
    {
        return max(0, $this->activeUserCount() - 10);
    }

    public function suspend(): bool
    {
        return $this->update(['is_suspended' => true]);
    }

    public function unsuspend(): bool
    {
        return $this->update(['is_suspended' => false]);
    }

    protected function casts(): array
    {
        return [
            'is_suspended' => 'boolean',
        ];
    }
}
