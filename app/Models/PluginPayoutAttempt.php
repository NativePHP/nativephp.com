<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginPayoutAttempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<PluginPayout, PluginPayoutAttempt>
     */
    public function pluginPayout(): BelongsTo
    {
        return $this->belongsTo(PluginPayout::class);
    }

    protected function casts(): array
    {
        return [
            'succeeded' => 'boolean',
        ];
    }
}
