<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PluginReportCategory;
use App\Enums\PluginReportStatus;
use App\Notifications\PluginReported;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Notification;

final class PluginReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @param  Builder<PluginReport>  $query
     * @return Builder<PluginReport>
     */
    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->where('status', PluginReportStatus::Open);
    }

    public static function hasOpenFor(Plugin $plugin, User $user): bool
    {
        return self::query()
            ->where('plugin_id', $plugin->id)
            ->where('user_id', $user->id)
            ->open()
            ->exists();
    }

    public static function file(Plugin $plugin, User $user, PluginReportCategory $category, string $message): self
    {
        $report = self::query()->create([
            'plugin_id' => $plugin->id,
            'user_id' => $user->id,
            'category' => $category,
            'message' => $message,
            'status' => PluginReportStatus::Open,
        ]);

        Notification::route('mail', config('mail.support_address'))
            ->notify(new PluginReported($report));

        return $report;
    }

    /**
     * @return BelongsTo<Plugin, PluginReport>
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * @return BelongsTo<User, PluginReport>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, PluginReport>
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isOpen(): bool
    {
        return $this->status === PluginReportStatus::Open;
    }

    public function resolve(User $admin, ?string $note = null): void
    {
        $this->update([
            'status' => PluginReportStatus::Resolved,
            'resolution_note' => $note,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);
    }

    public function dismiss(User $admin, ?string $note = null): void
    {
        $this->update([
            'status' => PluginReportStatus::Dismissed,
            'resolution_note' => $note,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);
    }

    protected function casts(): array
    {
        return [
            'category' => PluginReportCategory::class,
            'status' => PluginReportStatus::class,
            'resolved_at' => 'datetime',
        ];
    }
}
