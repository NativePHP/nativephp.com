<?php

namespace App\Models;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginActivity extends Model
{
    protected $guarded = [];

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
