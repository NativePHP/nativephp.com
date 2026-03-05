<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const INCLUDED_SEATS = 10;

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
    public function members(): HasMany
    {
        return $this->hasMany(TeamUser::class);
    }

    public function totalSeatCapacity(): int
    {
        return self::INCLUDED_SEATS + $this->extra_seats;
    }

    public function occupiedSeatCount(): int
    {
        return $this->members()
            ->whereIn('status', ['active', 'pending'])
            ->count();
    }

    public function availableSeats(): int
    {
        return $this->totalSeatCapacity() - $this->occupiedSeatCount();
    }

    public function hasAvailableSeats(): bool
    {
        return $this->availableSeats() > 0;
    }

    public function canRemoveExtraSeats(int $count): bool
    {
        $newCapacity = self::INCLUDED_SEATS + ($this->extra_seats - $count);

        return $this->occupiedSeatCount() <= $newCapacity;
    }

    protected $casts = [
        'is_suspended' => 'boolean',
        'extra_seats' => 'integer',
    ];
}
