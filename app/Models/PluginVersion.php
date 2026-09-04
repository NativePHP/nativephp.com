<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginVersion extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @param  Builder<PluginVersion>  $query
     * @return Builder<PluginVersion>
     */
    #[Scope]
    protected function visible(Builder $query): Builder
    {
        return $query->where('requires_review', false);
    }

    /**
     * @return BelongsTo<Plugin, PluginVersion>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @return BelongsTo<User, PluginVersion>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function approve(User $admin): void
    {
        $this->update([
            'requires_review' => false,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
    }

    protected function casts(): array
    {
        return [
            'is_packaged' => 'boolean',
            'packaged_at' => 'datetime',
            'published_at' => 'datetime',
            'manifest_permissions' => 'array',
            'permissions_expanded' => 'boolean',
            'requires_review' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }
}
