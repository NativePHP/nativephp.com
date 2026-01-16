<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginPayout extends Model
{
    use HasFactory;

    public const PLATFORM_FEE_PERCENT = 30;

    protected $guarded = [];

    protected $casts = [
        'gross_amount' => 'integer',
        'platform_fee' => 'integer',
        'developer_amount' => 'integer',
        'status' => PayoutStatus::class,
        'transferred_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<PluginLicense, PluginPayout>
     */
    public function pluginLicense(): BelongsTo
    {
        return $this->belongsTo(PluginLicense::class);
    }

    /**
     * @return BelongsTo<DeveloperAccount, PluginPayout>
     */
    public function developerAccount(): BelongsTo
    {
        return $this->belongsTo(DeveloperAccount::class);
    }

    /**
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Pending);
    }

    /**
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    public function scopeTransferred(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Transferred);
    }

    /**
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Failed);
    }

    /**
     * @return array{platform_fee: int, developer_amount: int}
     */
    public static function calculateSplit(int $grossAmount): array
    {
        $platformFee = (int) round($grossAmount * self::PLATFORM_FEE_PERCENT / 100);
        $developerAmount = $grossAmount - $platformFee;

        return [
            'platform_fee' => $platformFee,
            'developer_amount' => $developerAmount,
        ];
    }

    public function isPending(): bool
    {
        return $this->status === PayoutStatus::Pending;
    }

    public function isTransferred(): bool
    {
        return $this->status === PayoutStatus::Transferred;
    }

    public function isFailed(): bool
    {
        return $this->status === PayoutStatus::Failed;
    }

    public function markAsTransferred(string $stripeTransferId): void
    {
        $this->update([
            'status' => PayoutStatus::Transferred,
            'stripe_transfer_id' => $stripeTransferId,
            'transferred_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => PayoutStatus::Failed,
        ]);
    }
}
