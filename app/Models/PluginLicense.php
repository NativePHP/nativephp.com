<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PluginLicense extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<User, PluginLicense>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Plugin, PluginLicense>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @return HasOne<PluginPayout>
     */
    public function payout(): HasOne
    {
        return $this->hasOne(PluginPayout::class);
    }

    /**
     * @return BelongsTo<PluginBundle, PluginLicense>
     */
    public function pluginBundle(): BelongsTo
    {
        return $this->belongsTo(PluginBundle::class);
    }

    public function wasPurchasedAsBundle(): bool
    {
        return $this->plugin_bundle_id !== null;
    }

    /**
     * @param  Builder<PluginLicense>  $query
     * @return Builder<PluginLicense>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where(function ($q): void {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * @param  Builder<PluginLicense>  $query
     * @return Builder<PluginLicense>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * @param  Builder<PluginLicense>  $query
     * @return Builder<PluginLicense>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forPlugin(Builder $query, Plugin $plugin): Builder
    {
        return $query->where('plugin_id', $plugin->id);
    }

    public function isActive(): bool
    {
        if ($this->expires_at === null) {
            return true;
        }

        return $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return ! $this->isActive();
    }

    protected function casts(): array
    {
        return [
            'price_paid' => 'integer',
            'is_grandfathered' => 'boolean',
            'purchased_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
