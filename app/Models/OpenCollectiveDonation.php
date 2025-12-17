<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenCollectiveDonation extends Model
{
    use HasFactory;

    protected $table = 'opencollective_donations';

    protected $guarded = [];

    protected $casts = [
        'raw_payload' => 'array',
        'claimed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

    public function markAsClaimed(User $user): void
    {
        $this->update([
            'user_id' => $user->id,
            'claimed_at' => now(),
        ]);
    }
}
