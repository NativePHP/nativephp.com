<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
    protected function held(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Held);
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
     * @param  Builder<PluginPayout>  $query
     * @return Builder<PluginPayout>
     */
    #[Scope]
    protected function cancelled(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Cancelled);
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

    public function isHeld(): bool
    {
        return $this->status === PayoutStatus::Held;
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

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => PayoutStatus::Cancelled,
        ]);
    }

    public function isCancelled(): bool
    {
        return $this->status === PayoutStatus::Cancelled;
    }

    /**
     * When a pending payout should be transferred to the developer's Stripe account.
     * Older payouts may predate eligible_for_payout_at, so fall back to the 15-day hold.
     */
    public function expectedTransferDate(): ?Carbon
    {
        if (! $this->isPending()) {
            return null;
        }

        return $this->eligible_for_payout_at ?? $this->created_at->copy()->addDays(15);
    }

    public function wasCancelledByRefund(): bool
    {
        return $this->isCancelled() && $this->pluginLicense?->isRefunded();
    }

    /**
     * A cancelled payout that still has a transfer ID had its money sent to the
     * developer before the refund, so the refund reversed that transfer.
     */
    public function wasReversed(): bool
    {
        return $this->wasCancelledByRefund() && $this->stripe_transfer_id !== null;
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
