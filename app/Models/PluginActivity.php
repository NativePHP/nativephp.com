<?php

namespace App\Models;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginActivity extends Model
{
    protected $guarded = [];

    /**
     * @param  Builder<PluginActivity>  $query
     * @return Builder<PluginActivity>
     */
    #[Scope]
    protected function messages(Builder $query): Builder
    {
        return $query->whereIn('type', PluginActivityType::messageTypes());
    }

    /**
     * How this entry's causer should be described when the plugin's owner
     * (`$ownerId`) is the one reading the log.
     */
    public function causerNameFor(int $ownerId): string
    {
        return match (true) {
            $this->causer_id === $ownerId => 'you',
            $this->causer !== null => $this->causer->name,
            default => 'NativePHP',
        };
    }

    /**
     * @return BelongsTo<Plugin, PluginActivity>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @return BelongsTo<User, PluginActivity>
     */
    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    protected function casts(): array
    {
        return [
            'type' => PluginActivityType::class,
            'from_status' => PluginStatus::class,
            'to_status' => PluginStatus::class,
        ];
    }
}
