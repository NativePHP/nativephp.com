<?php

namespace App\Models;

use App\Enums\TeamUserRole;
use App\Enums\TeamUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'email',
        'role',
        'status',
        'invitation_token',
        'invited_at',
        'accepted_at',
    ];

    /**
     * @return BelongsTo<Team>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === TeamUserStatus::Active;
    }

    public function isPending(): bool
    {
        return $this->status === TeamUserStatus::Pending;
    }

    public function isRemoved(): bool
    {
        return $this->status === TeamUserStatus::Removed;
    }

    public function accept(User $user): void
    {
        $this->update([
            'user_id' => $user->id,
            'status' => TeamUserStatus::Active,
            'invitation_token' => null,
            'accepted_at' => now(),
        ]);
    }

    public function remove(): void
    {
        $this->update([
            'status' => TeamUserStatus::Removed,
            'invitation_token' => null,
        ]);
    }

    protected function casts(): array
    {
        return [
            'status' => TeamUserStatus::class,
            'role' => TeamUserRole::class,
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }
}
