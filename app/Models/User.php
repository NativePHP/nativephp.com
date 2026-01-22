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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'mobile_repo_access_granted_at' => 'datetime',
        'discord_role_granted_at' => 'datetime',
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
            ->whereHas('parentLicense', function ($query) {
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
     * Check if user has an active Pro or Max license (direct only, not sub-licenses).
     * Used to determine plugin discount eligibility.
     */
    public function hasActiveProOrMaxLicense(): bool
    {
        return $this->licenses()
            ->whereIn('policy_name', ['pro', 'max'])
            ->where('is_suspended', false)
            ->whereActive()
            ->exists();
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

        if ($this->hasActiveProOrMaxLicense()) {
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

    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name'] ?? $this->name ?? 'Unknown';
    }

    public function getFirstNameAttribute(): ?string
    {
        if (empty($this->name)) {
            return null;
        }

        $nameParts = explode(' ', $this->name, 2);

        return $nameParts[0];
    }

    public function getLastNameAttribute(): ?string
    {
        if (empty($this->name)) {
            return null;
        }

        $nameParts = explode(' ', $this->name, 2);

        return $nameParts[1] ?? null;
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
}
