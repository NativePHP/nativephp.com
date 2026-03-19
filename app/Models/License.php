<?php

namespace App\Models;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\SubscriptionItem;

class License extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function whereActive(Builder $builder): Builder
    {
        return $builder->where(fn ($where) => $where
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', now())
        );
    }

    /**
     * @return HasMany<SubLicense>
     */
    public function subLicenses(): HasMany
    {
        return $this->hasMany(SubLicense::class, 'parent_license_id');
    }

    /**
     * @return HasMany<LicenseExpiryWarning>
     */
    public function expiryWarnings(): HasMany
    {
        return $this->hasMany(LicenseExpiryWarning::class);
    }

    protected function anystackProductId(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return Subscription::from($this->policy_name)->anystackProductId();
        });
    }

    protected function subscriptionType(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return Subscription::from($this->policy_name);
        });
    }

    public function supportsSubLicenses(): bool
    {
        return $this->subscriptionType->supportsSubLicenses();
    }

    protected function subLicenseLimit(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return $this->subscriptionType->subLicenseLimit();
        });
    }

    protected function remainingSubLicenses(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $limit = $this->subLicenseLimit;
            if ($limit === null) {
                return null; // Unlimited
            }
            $used = $this->subLicenses()->where('is_suspended', false)->count();

            return max(0, $limit - $used);
        });
    }

    public function canCreateSubLicense(): bool
    {
        if (! $this->supportsSubLicenses()) {
            return false;
        }

        if ($this->is_suspended) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        $remaining = $this->remainingSubLicenses;

        return $remaining === null || $remaining > 0;
    }

    public function isLegacy(): bool
    {
        return ! $this->subscription_item_id
            && $this->created_at->lt(\Illuminate\Support\Facades\Date::create(2025, 5, 8));
    }

    public function suspendAllSubLicenses(): int
    {
        return $this->subLicenses()->update(['is_suspended' => true]);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::updated(function (self $license): void {
            // If parent license is suspended, suspend all sub-licenses
            if ($license->isDirty('is_suspended') && $license->is_suspended) {
                $license->suspendAllSubLicenses();
            }

            // If parent license expiry changed, update all sub-license expiry dates
            if ($license->isDirty('expires_at')) {
                $license->subLicenses()->update(['expires_at' => $license->expires_at]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_suspended' => 'boolean',
            'source' => LicenseSource::class,
        ];
    }
}
