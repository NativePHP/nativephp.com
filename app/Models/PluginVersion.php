<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginVersion extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<Plugin, PluginVersion>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    public function isPackaged(): bool
    {
        return $this->is_packaged;
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function getDownloadPath(): string
    {
        return $this->storage_path ?? '';
    }

    protected function casts(): array
    {
        return [
            'is_packaged' => 'boolean',
            'packaged_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }
}
