<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use Billable, HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'github_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return in_array($this->email, config('filament.users'), true);
    }

    /**
     * @return HasMany<License>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * @return HasMany<WallOfLoveSubmission>
     */
    public function wallOfLoveSubmissions(): HasMany
    {
        return $this->hasMany(WallOfLoveSubmission::class);
    }

    /**
     * @return HasMany<Plugin>
     */
    public function plugins(): HasMany
    {
        return $this->hasMany(Plugin::class);
    }

    /**
     * @return HasMany<PluginLicense>
     */
    public function pluginLicenses(): HasMany
    {
        return $this->hasMany(PluginLicense::class);
    }

    /**
     * @return HasMany<ProductLicense>
     */
    public function productLicenses(): HasMany
    {
        return $this->hasMany(ProductLicense::class);
    }

    /**
     * Check if user has a license for a specific product.
     */
    public function hasProductLicense(Product $product): bool
    {
        return $this->productLicenses()
            ->forProduct($product)
            ->exists();
    }

    /**
     * @return HasOne<DeveloperAccount>
     */
    public function developerAccount(): HasOne
    {
        return $this->hasOne(DeveloperAccount::class);
    }

    public function hasActiveMaxLicense(): bool
    {
        return $this->licenses()
            ->where('policy_name', 'max')
            ->where('is_suspended', false)
            ->whereActive()
            ->exists();
    }

    public function hasActiveMaxSubLicense(): bool
    {
        return SubLicense::query()
            ->where('assigned_email', $this->email)
            ->where('is_suspended', false)
            ->whereActive()
            ->whereHas('parentLicense', function ($query): void {
                $query->where('policy_name', 'max')
                    ->where('is_suspended', false)
                    ->whereActive();
            })
            ->exists();
    }

    public function hasMaxAccess(): bool
    {
        return $this->hasActiveMaxLicense() || $this->hasActiveMaxSubLicense();
    }

    /**
     * Check if user was an Early Access Program customer.
     * EAP customers purchased before June 1, 2025.
     */
    public function isEapCustomer(): bool
    {
        return $this->licenses()
            ->where('created_at', '<', '2025-06-01 00:00:00')
            ->exists();
    }

    /**
     * Get all price tiers the user is eligible for.
     * Always includes 'regular', plus any special tiers based on their status.
     *
     * @return array<\App\Enums\PriceTier>
     */
    public function getEligiblePriceTiers(): array
    {
        $tiers = [\App\Enums\PriceTier::Regular];

        if ($this->subscribed()) {
            $tiers[] = \App\Enums\PriceTier::Subscriber;
        }

        if ($this->isEapCustomer()) {
            $tiers[] = \App\Enums\PriceTier::Eap;
        }

        return $tiers;
    }

    public function hasDiscordConnected(): bool
    {
        return ! empty($this->discord_id);
    }

    public function hasActualLicense(): bool
    {
        return $this->licenses()->exists();
    }

    protected function displayName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return $this->attributes['display_name'] ?? $this->name ?? 'Unknown';
        });
    }

    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if (empty($this->name)) {
                return null;
            }
            $nameParts = explode(' ', $this->name, 2);

            return $nameParts[0];
        });
    }

    protected function lastName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if (empty($this->name)) {
                return null;
            }
            $nameParts = explode(' ', $this->name, 2);

            return $nameParts[1] ?? null;
        });
    }

    public function findStripeCustomerRecords(): Collection
    {
        $search = static::stripe()->customers->search([
            'query' => 'email:"'.$this->email.'"',
        ]);

        return collect($search->data);
    }

    public function getPluginLicenseKey(): string
    {
        if (! $this->plugin_license_key) {
            $this->plugin_license_key = bin2hex(random_bytes(32));
            $this->save();
        }

        return $this->plugin_license_key;
    }

    public function regeneratePluginLicenseKey(): string
    {
        $this->plugin_license_key = bin2hex(random_bytes(32));
        $this->save();

        return $this->plugin_license_key;
    }

    public function hasPluginAccess(Plugin $plugin): bool
    {
        if ($plugin->isFree()) {
            return true;
        }

        // Authors always have access to their own plugins
        if ($plugin->user_id === $this->id) {
            return true;
        }

        return $this->pluginLicenses()
            ->forPlugin($plugin)
            ->active()
            ->exists();
    }

    public function getGitHubToken(): ?string
    {
        if (! $this->github_token) {
            return null;
        }

        try {
            return decrypt($this->github_token);
        } catch (\Exception) {
            return null;
        }
    }

    public function hasGitHubToken(): bool
    {
        return $this->getGitHubToken() !== null;
    }

    /**
     * Plugin names that are available for free to eligible subscribers.
     */
    public const FREE_PLUGINS_OFFER = [
        'nativephp/mobile-biometrics',
        'nativephp/mobile-geolocation',
        'nativephp/mobile-firebase',
        'nativephp/mobile-secure-storage',
        'nativephp/mobile-scanner',
    ];

    /**
     * Check if user is eligible for the free plugins offer.
     * Eligible if they purchased or renewed since Nov 1st 2025.
     */
    public function isEligibleForFreePluginsOffer(): bool
    {
        $cutoffDate = '2025-11-01 00:00:00';

        // Check for licenses created since the cutoff (new purchases)
        $hasRecentLicense = $this->licenses()
            ->where('created_at', '>=', $cutoffDate)
            ->exists();

        if ($hasRecentLicense) {
            return true;
        }

        // Check for active subscription renewals (subscription updated since cutoff)
        // This catches renewals where the subscription was updated
        $hasRecentRenewal = $this->subscriptions()
            ->where('stripe_status', 'active')
            ->where('updated_at', '>=', $cutoffDate)
            ->exists();

        return $hasRecentRenewal;
    }

    /**
     * Check if user has already claimed all free plugins.
     */
    public function hasClaimedFreePlugins(): bool
    {
        $freePluginIds = \App\Models\Plugin::query()
            ->whereIn('name', self::FREE_PLUGINS_OFFER)
            ->pluck('id');

        if ($freePluginIds->isEmpty()) {
            return false;
        }

        // Check if user has licenses for all the free plugins
        $claimedCount = $this->pluginLicenses()
            ->whereIn('plugin_id', $freePluginIds)
            ->count();

        return $claimedCount >= $freePluginIds->count();
    }

    /**
     * Check if user should see the free plugins offer banner.
     * Offer expires on 31st May 2026.
     */
    public function shouldSeeFreePluginsOffer(): bool
    {
        $offerExpiresAt = '2026-05-31 23:59:59';

        if (now()->gt($offerExpiresAt)) {
            return false;
        }

        return $this->isEligibleForFreePluginsOffer() && ! $this->hasClaimedFreePlugins();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'mobile_repo_access_granted_at' => 'datetime',
            'claude_plugins_repo_access_granted_at' => 'datetime',
            'discord_role_granted_at' => 'datetime',
        ];
    }
}
