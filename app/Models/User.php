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
