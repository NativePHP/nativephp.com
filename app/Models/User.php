<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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

    public function hasDiscordConnected(): bool
    {
        return ! empty($this->discord_id);
    }

    public function hasActualLicense(): bool
    {
        return $this->licenses()->exists();
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
}
