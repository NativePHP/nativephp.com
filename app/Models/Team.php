<?php

namespace App\Models;

use App\Enums\TeamUserStatus;
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
        'extra_seats',
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
            ->where('status', TeamUserStatus::Active);
    }

    /**
     * @return HasMany<TeamUser>
     */
    public function pendingInvitations(): HasMany
    {
        return $this->hasMany(TeamUser::class)
            ->where('status', TeamUserStatus::Pending);
    }

    public function activeUserCount(): int
    {
        return $this->activeUsers()->count();
    }

    public function includedSeats(): int
    {
        return config('subscriptions.plans.max.included_seats', 10);
    }

    public function totalSeatCapacity(): int
    {
        return $this->includedSeats() + ($this->extra_seats ?? 0);
    }

    public function occupiedSeatCount(): int
    {
        return 1 + $this->activeUserCount() + $this->pendingInvitations()->count();
    }

    public function availableSeats(): int
    {
        return max(0, $this->totalSeatCapacity() - $this->occupiedSeatCount());
    }

    public function isOverIncludedLimit(): bool
    {
        return $this->occupiedSeatCount() >= $this->totalSeatCapacity();
    }

    public function extraSeatsCount(): int
    {
        return max(0, 1 + $this->activeUserCount() - $this->includedSeats());
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
