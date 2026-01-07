<?php

namespace App\Models;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Notifications\PluginApproved;
use App\Notifications\PluginRejected;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plugin extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => PluginStatus::class,
        'type' => PluginType::class,
        'approved_at' => 'datetime',
        'featured' => 'boolean',
        'composer_data' => 'array',
        'nativephp_data' => 'array',
        'last_synced_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (Plugin $plugin) {
            $plugin->recordActivity(
                PluginActivityType::Submitted,
                null,
                PluginStatus::Pending,
                null,
                $plugin->user_id
            );
        });
    }

    /**
     * @return BelongsTo<User, Plugin>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, Plugin>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<PluginActivity>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(PluginActivity::class)->orderBy('created_at', 'desc');
    }

    public function isPending(): bool
    {
        return $this->status === PluginStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === PluginStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === PluginStatus::Rejected;
    }

    public function isFree(): bool
    {
        return $this->type === PluginType::Free;
    }

    public function isPaid(): bool
    {
        return $this->type === PluginType::Paid;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * @param  Builder<Plugin>  $query
     * @return Builder<Plugin>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PluginStatus::Approved);
    }

    /**
     * @param  Builder<Plugin>  $query
     * @return Builder<Plugin>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function getPackagistUrl(): string
    {
        return "https://packagist.org/packages/{$this->name}";
    }

    public function getGithubUrl(): string
    {
        return "https://github.com/{$this->name}";
    }

    public function getAnystackUrl(): ?string
    {
        if (! $this->anystack_id) {
            return null;
        }

        return "https://anystack.sh/products/{$this->anystack_id}";
    }

    public function getWebhookUrl(): ?string
    {
        if (! $this->webhook_secret) {
            return null;
        }

        return route('webhooks.plugins', $this->webhook_secret);
    }

    public function generateWebhookSecret(): string
    {
        $secret = bin2hex(random_bytes(32));

        $this->update(['webhook_secret' => $secret]);

        return $secret;
    }

    public function getRepositoryOwnerAndName(): ?array
    {
        if (! $this->repository_url) {
            return null;
        }

        $path = parse_url($this->repository_url, PHP_URL_PATH);
        $parts = array_values(array_filter(explode('/', trim($path, '/'))));

        if (count($parts) < 2) {
            return null;
        }

        return [
            'owner' => $parts[0],
            'repo' => str_replace('.git', '', $parts[1]),
        ];
    }

    public function approve(int $approvedById): void
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => PluginStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $approvedById,
            'rejection_reason' => null,
        ]);

        $this->recordActivity(
            PluginActivityType::Approved,
            $previousStatus,
            PluginStatus::Approved,
            null,
            $approvedById
        );

        $this->user->notify(new PluginApproved($this));
    }

    public function reject(string $reason, int $rejectedById): void
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => PluginStatus::Rejected,
            'rejection_reason' => $reason,
            'approved_at' => null,
            'approved_by' => $rejectedById,
        ]);

        $this->recordActivity(
            PluginActivityType::Rejected,
            $previousStatus,
            PluginStatus::Rejected,
            $reason,
            $rejectedById
        );

        $this->user->notify(new PluginRejected($this));
    }

    public function resubmit(): void
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => PluginStatus::Pending,
            'rejection_reason' => null,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $this->recordActivity(
            PluginActivityType::Resubmitted,
            $previousStatus,
            PluginStatus::Pending,
            null,
            $this->user_id
        );
    }

    public function updateDescription(string $description, int $updatedById): void
    {
        $oldDescription = $this->description;

        $this->update([
            'description' => $description,
        ]);

        $this->activities()->create([
            'type' => PluginActivityType::DescriptionUpdated,
            'from_status' => $this->status->value,
            'to_status' => $this->status->value,
            'note' => $oldDescription ? "Changed from: {$oldDescription}" : 'Initial description set',
            'causer_id' => $updatedById,
        ]);
    }

    protected function recordActivity(
        PluginActivityType $type,
        ?PluginStatus $fromStatus,
        PluginStatus $toStatus,
        ?string $note,
        ?int $causerId
    ): void {
        $this->activities()->create([
            'type' => $type,
            'from_status' => $fromStatus?->value,
            'to_status' => $toStatus->value,
            'note' => $note,
            'causer_id' => $causerId,
        ]);
    }
}
