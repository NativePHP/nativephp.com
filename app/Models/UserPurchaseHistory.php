<?php

namespace App\Models;

use App\Enums\GrandfatheringTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPurchaseHistory extends Model
{
    use HasFactory;

    protected $table = 'user_purchase_history';

    protected $guarded = [];

    protected $casts = [
        'total_spent' => 'integer',
        'first_purchase_at' => 'datetime',
        'grandfathering_tier' => GrandfatheringTier::class,
        'recalculated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, UserPurchaseHistory>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPurchaseHistory(): bool
    {
        return $this->first_purchase_at !== null;
    }

    public function getTier(): GrandfatheringTier
    {
        return $this->grandfathering_tier ?? GrandfatheringTier::None;
    }
}
