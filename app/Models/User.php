<?php

namespace App\Models;

use App\Enums\PriceTier;
use App\Enums\Subscription;
use App\Enums\TeamUserStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    use Billable, HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'github_token',
    ];

    public function getFilamentName(): string
    {
        return $this->attributes['display_name'] ?? $this->name ?? $this->email;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return in_array($this->email, config('filament.users'), true);
    }

    /**
     * @return HasOne<Team>
     */
    public function ownedTeam(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    /**
     * Get the team owner if this user is an active team member.
     */
    public function getTeamOwner(): ?self
    {
        $membership = $this->activeTeamMembership();

        if (! $membership) {
            return null;
        }

        return $membership->team->owner;
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
        if ($this->productLicenses()->forProduct($product)->exists()) {
            return true;
        }

        return $this->hasProductAccessViaTeam($product);
    }

    /**
     * @return HasMany<LessonProgress>
     */
    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * @return HasOne<DeveloperAccount>
     */
    public function developerAccount(): HasOne
    {
        return $this->hasOne(DeveloperAccount::class);
    }

    /**
     * @return HasMany<TeamUser>
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamUser::class);
    }

    public function isUltraTeamMember(): bool
    {
        // Team owners count as members
        if ($this->ownedTeam && ! $this->ownedTeam->is_suspended) {
            return true;
        }

        return TeamUser::query()
            ->where('user_id', $this->id)
            ->where('status', TeamUserStatus::Active)
            ->whereHas('team', fn ($query) => $query->where('is_suspended', false))
            ->exists();
    }

    public function activeTeamMembership(): ?TeamUser
    {
        return TeamUser::query()
            ->where('user_id', $this->id)
            ->where('status', TeamUserStatus::Active)
            ->whereHas('team', fn ($query) => $query->where('is_suspended', false))
            ->with('team')
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, TeamUser>
     */
    public function activeTeamMemberships(): \Illuminate\Database\Eloquent\Collection
    {
        return TeamUser::query()
            ->where('user_id', $this->id)
            ->where('status', TeamUserStatus::Active)
            ->whereHas('team', fn ($query) => $query->where('is_suspended', false))
            ->with('team')
            ->get();
    }

    public function hasProductAccessViaTeam(Product $product): bool
    {
        $membership = $this->activeTeamMembership();

        if (! $membership) {
            return false;
        }

        // Check the owner's direct product licenses only (not via team) to avoid recursion
        return $membership->team->owner->productLicenses()
            ->forProduct($product)
            ->exists();
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
     * Check if the user should have access to the nativephp/mobile repository.
     * Max license holders always have access. Ultra subscribers only qualify
     * if their subscription was created before February 1, 2026.
     */
    public function hasMobileRepoAccess(): bool
    {
        if ($this->hasActiveMaxLicense() || $this->hasActiveMaxSubLicense()) {
            return true;
        }

        $subscription = $this->subscription();

        if (! $subscription || ! $subscription->active()) {
            return false;
        }

        return $subscription->created_at->lt('2026-02-01 00:00:00');
    }

    /**
     * Check if the user's subscription is a comped (free) subscription.
     * Covers both legacy comped (is_comped flag) and comped Ultra price.
     */
    public function hasCompedSubscription(): bool
    {
        $subscription = $this->subscription();

        if (! $subscription || ! $subscription->active()) {
            return false;
        }

        if ($subscription->is_comped) {
            return true;
        }

        $compedPriceId = config('subscriptions.plans.max.stripe_price_id_comped');

        return $compedPriceId && $this->subscribedToPrice($compedPriceId);
    }

    public function hasActiveUltraSubscription(): bool
    {
        $subscription = $this->subscription();

        if (! $subscription) {
            return false;
        }

        // Comped Ultra subs use a dedicated price — always grant Ultra access
        $compedUltraPriceId = config('subscriptions.plans.max.stripe_price_id_comped');

        if ($compedUltraPriceId && $this->subscribedToPrice($compedUltraPriceId)) {
            return true;
        }

        // Legacy comped Max subs should not get Ultra access
        if ($subscription->is_comped) {
            return false;
        }

        return $this->subscribedToPrice(array_filter([
            config('subscriptions.plans.max.stripe_price_id'),
            config('subscriptions.plans.max.stripe_price_id_monthly'),
            config('subscriptions.plans.max.stripe_price_id_eap'),
            config('subscriptions.plans.max.stripe_price_id_discounted'),
        ]));
    }

    /**
     * Check if the user has Ultra access (paying or comped Ultra),
     * qualifying them for Ultra benefits like Teams and free plugins.
     */
    public function hasUltraAccess(): bool
    {
        $subscription = $this->subscription();

        if (! $subscription || ! $subscription->active()) {
            return false;
        }

        // Comped Ultra subs always get full access
        $compedUltraPriceId = config('subscriptions.plans.max.stripe_price_id_comped');

        if ($compedUltraPriceId && $this->subscribedToPrice($compedUltraPriceId)) {
            return true;
        }

        $planPriceId = $subscription->stripe_price;

        if (! $planPriceId) {
            foreach ($subscription->items as $item) {
                if (! Subscription::isExtraSeatPrice($item->stripe_price)) {
                    $planPriceId = $item->stripe_price;
                    break;
                }
            }
        }

        if (! $planPriceId) {
            return false;
        }

        try {
            if (Subscription::fromStripePriceId($planPriceId) !== Subscription::Max) {
                return false;
            }
        } catch (\RuntimeException) {
            return false;
        }

        // Legacy comped Max subs don't get Ultra access
        return ! $subscription->is_comped;
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
     * @return array<PriceTier>
     */
    public function getEligiblePriceTiers(): array
    {
        $tiers = [PriceTier::Regular];

        if ($this->subscribed()) {
            $tiers[] = PriceTier::Subscriber;
        }

        if ($this->isEapCustomer()) {
            $tiers[] = PriceTier::Eap;
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

    protected function displayName(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->attributes['display_name'] ?? $this->name ?? 'Unknown';
        });
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(get: function () {
            if (empty($this->name)) {
                return null;
            }
            $nameParts = explode(' ', $this->name, 2);

            return $nameParts[0];
        });
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(get: function () {
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

        if ($this->pluginLicenses()->forPlugin($plugin)->active()->exists()) {
            return true;
        }

        // Ultra subscribers and team members get access to all official (first-party) plugins
        if ($plugin->isOfficial() && ($this->hasUltraAccess() || $this->isUltraTeamMember())) {
            return true;
        }

        // Team members get access to plugins the team owner has purchased
        $teamOwner = $this->getTeamOwner();

        if ($teamOwner && $teamOwner->pluginLicenses()->forPlugin($plugin)->active()->exists()) {
            return true;
        }

        return false;
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
        $freePluginIds = Plugin::query()
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
            'receives_notification_emails' => 'boolean',
            'receives_new_plugin_notifications' => 'boolean',
            'mobile_repo_access_granted_at' => 'datetime',
            'claude_plugins_repo_access_granted_at' => 'datetime',
            'discord_role_granted_at' => 'datetime',
        ];
    }
}
