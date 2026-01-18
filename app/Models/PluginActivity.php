<?php

namespace App\Models;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginActivity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => PluginActivityType::class,
        'from_status' => PluginStatus::class,
        'to_status' => PluginStatus::class,
    ];

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
}
