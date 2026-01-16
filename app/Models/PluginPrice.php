<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginPrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<Plugin, PluginPrice>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @param  Builder<PluginPrice>  $query
     * @return Builder<PluginPrice>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 2);
    }

    public function getDiscountedAmount(int $discountPercent): int
    {
        if ($discountPercent <= 0 || $discountPercent > 100) {
            return $this->amount;
        }

        return (int) round($this->amount * (100 - $discountPercent) / 100);
    }
}
