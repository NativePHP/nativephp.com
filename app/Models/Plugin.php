<?php

namespace App\Models;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Notifications\PluginApproved;
use App\Notifications\PluginRejected;
use App\Services\PluginSyncService;
use App\Services\SatisService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plugin extends Model
{
    use HasFactory;

    /**
     * Find a plugin by its vendor and package name.
     */
    public static function findByVendorPackage(string $vendor, string $package): ?self
    {
        return static::where('name', "{$vendor}/{$package}")->first();
    }

    /**
     * Find a plugin by its vendor and package name, or fail.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByVendorPackageOrFail(string $vendor, string $package): self
    {
        return static::where('name', "{$vendor}/{$package}")->firstOrFail();
    }

    /**
     * Get route parameters for this plugin's vendor/package URL.
     *
     * @return array{vendor: string, package: string}
     */
    public function routeParams(): array
    {
        [$vendor, $package] = explode('/', $this->name);

        return ['vendor' => $vendor, 'package' => $package];
    }

    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (Plugin $plugin): void {
            $plugin->recordActivity(
                PluginActivityType::Submitted,
                null,
                PluginStatus::Pending,
                null,
                $plugin->user_id
            );
        });

        static::updated(function (Plugin $plugin): void {
            // When tier is set or changed, create/update prices automatically
            if ($plugin->wasChanged('tier') && $plugin->tier !== null) {
                $plugin->syncPricesFromTier();
            }
        });

        static::deleting(function (Plugin $plugin): void {
            // Remove from Satis when plugin is deleted
            if ($plugin->name) {
                resolve(SatisService::class)->removePackage($plugin->name);
            }
        });
    }

    /**
     * Create or update prices based on the plugin's tier.
     */
    public function syncPricesFromTier(): void
    {
        if ($this->tier === null) {
            return;
        }

        $tierPrices = $this->tier->getPrices();

        foreach ($tierPrices as $priceTier => $amount) {
            $this->prices()->updateOrCreate(
                ['tier' => $priceTier],
                [
                    'amount' => $amount,
                    'currency' => 'USD',
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * @return BelongsTo<User, Plugin>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, Plugin>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<PluginActivity>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(PluginActivity::class)->latest();
    }

    /**
     * @return BelongsTo<DeveloperAccount, Plugin>
     */
    public function developerAccount(): BelongsTo
    {
        return $this->belongsTo(DeveloperAccount::class);
    }

    /**
     * @return HasMany<PluginPrice>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(PluginPrice::class);
    }

    /**
     * Get the active price, preferring the Regular tier for consistency.
     *
     * @return HasOne<PluginPrice>
     */
    public function activePrice(): HasOne
    {
        return $this->hasOne(PluginPrice::class)
            ->where('is_active', true)
            ->orderByRaw("CASE WHEN tier = 'regular' THEN 0 ELSE 1 END")
            ->latest();
    }

    /**
     * Get the best (lowest) active price for a user based on their eligible tiers.
     * Returns null if no price exists for the user's eligible tiers.
     */
    public function getBestPriceForUser(?User $user): ?PluginPrice
    {
        $eligibleTiers = $user ? $user->getEligiblePriceTiers() : [\App\Enums\PriceTier::Regular];

        // Get the lowest active price for the user's eligible tiers
        return $this->prices()
            ->active()
            ->forTiers($eligibleTiers)
            ->orderBy('amount', 'asc')
            ->first();
    }

    /**
     * Check if a user has access to at least one price tier for this plugin.
     */
    public function hasAccessiblePriceFor(?User $user): bool
    {
        return $this->getBestPriceForUser($user) !== null;
    }

    /**
     * Get the regular (non-discounted) price for comparison display.
     */
    public function getRegularPrice(): ?PluginPrice
    {
        return $this->prices()
            ->active()
            ->forTier(\App\Enums\PriceTier::Regular)
            ->first() ?? $this->activePrice;
    }

    /**
     * @return HasMany<PluginLicense>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(PluginLicense::class);
    }

    /**
     * @return BelongsToMany<PluginBundle>
     */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(PluginBundle::class, 'bundle_plugin')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Alias for bundles() - required by Filament's AttachAction.
     *
     * @return BelongsToMany<PluginBundle>
     */
    public function pluginBundles(): BelongsToMany
    {
        return $this->bundles();
    }

    /**
     * @return HasMany<PluginVersion>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(PluginVersion::class)->latest();
    }

    /**
     * @return HasOne<PluginVersion>
     */
    public function latestVersion(): HasOne
    {
        return $this->hasOne(PluginVersion::class)->where('is_packaged', true)->latest('published_at');
    }

    public function isPending(): bool
    {
        return $this->status === PluginStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === PluginStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === PluginStatus::Rejected;
    }

    public function isFree(): bool
    {
        return $this->type === PluginType::Free;
    }

    public function isPaid(): bool
    {
        return $this->type === PluginType::Paid;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function isOfficial(): bool
    {
        return $this->is_official ?? false;
    }

    public function isSatisSynced(): bool
    {
        return $this->satis_synced_at !== null;
    }

    /**
     * @param  Builder<Plugin>  $query
     * @return Builder<Plugin>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function approved(Builder $query): Builder
    {
        return $query->where('status', PluginStatus::Approved)
            ->where('is_active', true);
    }

    /**
     * @param  Builder<Plugin>  $query
     * @return Builder<Plugin>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Plugin>  $query
     * @return Builder<Plugin>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function getPackagistUrl(): string
    {
        return "https://packagist.org/packages/{$this->name}";
    }

    public function getGithubUrl(): string
    {
        return "https://github.com/{$this->name}";
    }

    public function getWebhookUrl(): ?string
    {
        if (! $this->webhook_secret) {
            return null;
        }

        return route('webhooks.plugins', $this->webhook_secret);
    }

    public function getLogoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return asset('storage/'.$this->logo_path);
    }

    public function hasLogo(): bool
    {
        return $this->logo_path !== null;
    }

    public function hasGradientIcon(): bool
    {
        return $this->icon_gradient !== null && $this->icon_name !== null;
    }

    public function hasCustomIcon(): bool
    {
        return $this->hasLogo() || $this->hasGradientIcon();
    }

    /**
     * Available gradient presets for plugin icons.
     *
     * @return array<string, string>
     */
    public static function gradientPresets(): array
    {
        return [
            'indigo-purple' => 'from-indigo-500 to-purple-600',
            'blue-cyan' => 'from-blue-500 to-cyan-500',
            'green-emerald' => 'from-green-500 to-emerald-600',
            'orange-red' => 'from-orange-500 to-red-600',
            'pink-rose' => 'from-pink-500 to-rose-600',
            'violet-fuchsia' => 'from-violet-500 to-fuchsia-600',
            'amber-yellow' => 'from-amber-500 to-yellow-500',
            'slate-gray' => 'from-slate-600 to-gray-700',
        ];
    }

    public function getGradientClasses(): string
    {
        $presets = self::gradientPresets();

        return $presets[$this->icon_gradient] ?? $presets['indigo-purple'];
    }

    /**
     * Reserved namespaces that can only be used by admin users.
     */
    public const RESERVED_NAMESPACES = [
        'native',
        'nativephp',
        'bifrost',
    ];

    /**
     * Get the vendor namespace from the package name.
     * e.g., "acme/my-plugin" returns "acme"
     */
    public function getVendorNamespace(): ?string
    {
        if (! $this->name) {
            return null;
        }

        $parts = explode('/', $this->name);

        return $parts[0] ?? null;
    }

    /**
     * Check if a namespace is reserved (admin-only).
     */
    public static function isReservedNamespace(string $namespace): bool
    {
        return in_array(strtolower($namespace), self::RESERVED_NAMESPACES, true);
    }

    /**
     * Check if a vendor namespace is available for a given user.
     * Returns true if the namespace is not claimed by another user
     * and is not a reserved namespace (unless user is admin).
     */
    public static function isNamespaceAvailableForUser(string $namespace, int $userId): bool
    {
        $user = User::find($userId);

        // Reserved namespaces are only available to admins
        if (self::isReservedNamespace($namespace)) {
            return $user && $user->isAdmin();
        }

        // Check if namespace is already claimed by another user
        return ! static::where('name', 'like', $namespace.'/%')
            ->where('user_id', '!=', $userId)
            ->exists();
    }

    /**
     * Get the user who owns a particular namespace.
     */
    public static function getNamespaceOwner(string $namespace): ?User
    {
        $plugin = static::where('name', 'like', $namespace.'/%')
            ->first();

        return $plugin?->user;
    }

    public function getLicense(): ?string
    {
        return $this->composer_data['license'] ?? null;
    }

    public function getLicenseUrl(): ?string
    {
        $repoInfo = $this->getRepositoryOwnerAndName();

        if (! $repoInfo) {
            return null;
        }

        return "https://github.com/{$repoInfo['owner']}/{$repoInfo['repo']}/blob/main/LICENSE";
    }

    public function generateWebhookSecret(): string
    {
        $secret = bin2hex(random_bytes(32));

        $this->update(['webhook_secret' => $secret]);

        return $secret;
    }

    public function getRepositoryOwnerAndName(): ?array
    {
        if (! $this->repository_url) {
            return null;
        }

        $path = parse_url($this->repository_url, PHP_URL_PATH);
        $parts = array_values(array_filter(explode('/', trim($path, '/'))));

        if (count($parts) < 2) {
            return null;
        }

        return [
            'owner' => $parts[0],
            'repo' => str_replace('.git', '', $parts[1]),
        ];
    }

    public function approve(int $approvedById): void
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => PluginStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $approvedById,
            'rejection_reason' => null,
        ]);

        $this->recordActivity(
            PluginActivityType::Approved,
            $previousStatus,
            PluginStatus::Approved,
            null,
            $approvedById
        );

        $this->user->notify(new PluginApproved($this));

        resolve(PluginSyncService::class)->sync($this);
    }

    public function reject(string $reason, int $rejectedById): void
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => PluginStatus::Rejected,
            'rejection_reason' => $reason,
            'approved_at' => null,
            'approved_by' => $rejectedById,
        ]);

        $this->recordActivity(
            PluginActivityType::Rejected,
            $previousStatus,
            PluginStatus::Rejected,
            $reason,
            $rejectedById
        );

        $this->user->notify(new PluginRejected($this));
    }

    public function resubmit(): void
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => PluginStatus::Pending,
            'rejection_reason' => null,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $this->recordActivity(
            PluginActivityType::Resubmitted,
            $previousStatus,
            PluginStatus::Pending,
            null,
            $this->user_id
        );
    }

    public function updateDescription(string $description, int $updatedById): void
    {
        $oldDescription = $this->description;

        $this->update([
            'description' => $description,
        ]);

        $this->activities()->create([
            'type' => PluginActivityType::DescriptionUpdated,
            'from_status' => $this->status->value,
            'to_status' => $this->status->value,
            'note' => $oldDescription ? "Changed from: {$oldDescription}" : 'Initial description set',
            'causer_id' => $updatedById,
        ]);
    }

    protected function recordActivity(
        PluginActivityType $type,
        ?PluginStatus $fromStatus,
        PluginStatus $toStatus,
        ?string $note,
        ?int $causerId
    ): void {
        $this->activities()->create([
            'type' => $type,
            'from_status' => $fromStatus?->value,
            'to_status' => $toStatus->value,
            'note' => $note,
            'causer_id' => $causerId,
        ]);
    }

    protected function casts(): array
    {
        return [
            'status' => PluginStatus::class,
            'type' => PluginType::class,
            'tier' => PluginTier::class,
            'approved_at' => 'datetime',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'is_official' => 'boolean',
            'composer_data' => 'array',
            'nativephp_data' => 'array',
            'last_synced_at' => 'datetime',
            'satis_synced_at' => 'datetime',
            'webhook_installed' => 'boolean',
            'review_checks' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }
}
