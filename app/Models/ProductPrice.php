<?php

namespace App\Models;

use App\Enums\PriceTier;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

class ProductPrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<Product, ProductPrice>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param  Builder<ProductPrice>  $query
     * @return Builder<ProductPrice>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<ProductPrice>  $query
     * @return Builder<ProductPrice>
     */
    #[Scope]
    protected function forTier(Builder $query, PriceTier|string $tier): Builder
    {
        $tierValue = $tier instanceof PriceTier ? $tier->value : $tier;

        return $query->where('tier', $tierValue);
    }

    /**
     * @param  Builder<ProductPrice>  $query
     * @param  array<PriceTier>  $tiers
     * @return Builder<ProductPrice>
     */
    #[Scope]
    protected function forTiers(Builder $query, array $tiers): Builder
    {
        $tierValues = array_map(fn ($t) => $t instanceof PriceTier ? $t->value : $t, $tiers);

        return $query->whereIn('tier', $tierValues);
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(get: function () {
            return number_format($this->amount / 100, 2);
        });
    }

    /**
     * Marketing-style amount: whole dollars without decimals ("299"), otherwise two decimals ("49.99").
     */
    protected function displayAmount(): Attribute
    {
        return Attribute::make(get: fn () => self::formatAmountForDisplay($this->amount));
    }

    public static function formatAmountForDisplay(int $amount): string
    {
        return $amount % 100 === 0
            ? number_format($amount / 100)
            : number_format($amount / 100, 2);
    }

    /**
     * The amount after deducting the attached Stripe coupon's discount.
     * Falls back to the full amount when no coupon is attached or it can't be resolved.
     */
    public function discountedAmount(): int
    {
        $discount = $this->stripeCouponDiscount();

        if ($discount === false) {
            return $this->amount;
        }

        if ($discount['amount_off']) {
            return max(0, $this->amount - $discount['amount_off']);
        }

        if ($discount['percent_off']) {
            return (int) round($this->amount * (1 - $discount['percent_off'] / 100));
        }

        return $this->amount;
    }

    public function discountedDisplayAmount(): string
    {
        return self::formatAmountForDisplay($this->discountedAmount());
    }

    /**
     * Fetch the attached coupon's discount from Stripe, cached briefly.
     * Returns false (also cached, to avoid hammering Stripe with a bad ID)
     * when there is no coupon or it is invalid/unresolvable.
     *
     * @return array{amount_off: ?int, percent_off: ?float}|false
     */
    protected function stripeCouponDiscount(): array|false
    {
        if (! $this->stripe_coupon_id) {
            return false;
        }

        return Cache::remember(
            "stripe-coupon.{$this->stripe_coupon_id}",
            now()->addMinutes(10),
            function (): array|false {
                try {
                    $coupon = Cashier::stripe()->coupons->retrieve($this->stripe_coupon_id);

                    if (! $coupon->valid) {
                        return false;
                    }

                    return [
                        'amount_off' => $coupon->amount_off,
                        'percent_off' => $coupon->percent_off,
                    ];
                } catch (\Throwable $e) {
                    Log::warning('Failed to retrieve Stripe coupon for product price', [
                        'product_price_id' => $this->id,
                        'stripe_coupon_id' => $this->stripe_coupon_id,
                        'error' => $e->getMessage(),
                    ]);

                    return false;
                }
            }
        );
    }

    public function isRegularTier(): bool
    {
        return $this->tier === PriceTier::Regular;
    }

    public function isSubscriberTier(): bool
    {
        return $this->tier === PriceTier::Subscriber;
    }

    public function isEapTier(): bool
    {
        return $this->tier === PriceTier::Eap;
    }

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'is_active' => 'boolean',
            'tier' => PriceTier::class,
        ];
    }
}
