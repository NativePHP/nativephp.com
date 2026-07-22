<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PluginPayout extends Model
{
    use HasFactory;

    public const PLATFORM_FEE_PERCENT = 30;

    protected $guarded = [];

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
     * @return HasMany<PluginPayoutAttempt>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(PluginPayoutAttempt::class);
    }

    /**
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Pending);
    }

    /**
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    #[Scope]
    protected function transferred(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Transferred);
    }

    /**
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    #[Scope]
    protected function failed(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Failed);
    }

    /**
     * @return array{platform_fee: int, developer_amount: int}
     */
    public static function calculateSplit(int $grossAmount, int $platformFeePercent = self::PLATFORM_FEE_PERCENT): array
    {
        $platformFee = (int) round($grossAmount * $platformFeePercent / 100);
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
            'failure_reason' => null,
        ]);
    }

    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => PayoutStatus::Failed,
            'failure_reason' => $reason,
        ]);
    }

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'platform_fee' => 'integer',
            'developer_amount' => 'integer',
            'status' => PayoutStatus::class,
            'transferred_at' => 'datetime',
            'eligible_for_payout_at' => 'datetime',
            'last_attempted_at' => 'datetime',
            'attempt_count' => 'integer',
        ];
    }
}
