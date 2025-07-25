<?php

namespace App\Models;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use Illuminate\Database\Eloquent\Builder;
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
        'is_suspended' => 'boolean',
        'source' => LicenseSource::class,
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

    public function scopeWhereActive(Builder $builder): Builder
    {
        return $builder->where(fn ($where) => $where
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', now())
        );
    }

    public function getAnystackProductIdAttribute(): string
    {
        return Subscription::from($this->policy_name)->anystackProductId();
    }
}
