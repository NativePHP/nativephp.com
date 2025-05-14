<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\SubscriptionItem;

class License extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<SubscriptionItem>
     */
    public function subscriptionItem(): BelongsTo
    {
        return $this->belongsTo(SubscriptionItem::class);
    }
}
