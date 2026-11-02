<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PluginRating extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        self::saved(function (PluginRating $rating): void {
            $rating->plugin->recalculateRating();
        });

        self::deleted(function (PluginRating $rating): void {
            $rating->plugin->recalculateRating();
        });
    }

    public static function findFor(Plugin $plugin, User $user): ?self
    {
        return self::query()
            ->where('plugin_id', $plugin->id)
            ->where('user_id', $user->id)
            ->first();
    }

    public static function submit(Plugin $plugin, User $user, int $rating): self
    {
        return self::query()->updateOrCreate(
            ['plugin_id' => $plugin->id, 'user_id' => $user->id],
            ['rating' => $rating]
        );
    }

    /**
     * @return BelongsTo<Plugin, PluginRating>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @return BelongsTo<User, PluginRating>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }
}
