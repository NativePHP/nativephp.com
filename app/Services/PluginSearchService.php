<?php

namespace App\Services;

use App\Enums\PluginType;
use App\Enums\PriceTier;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class PluginSearchService
{
    public const int DEFAULT_LIMIT = 10;

    public const int MAX_LIMIT = 25;

    /**
     * Resolve a marketplace user from Basic Auth or explicit credentials.
     *
     * @throws ValidationException when credentials are partially provided or invalid
     */
    public function resolveUser(?string $email, ?string $licenseKey): ?User
    {
        $email = is_string($email) ? trim($email) : null;
        $licenseKey = is_string($licenseKey) ? trim($licenseKey) : null;

        $email = $email === '' ? null : $email;
        $licenseKey = $licenseKey === '' ? null : $licenseKey;

        if ($email === null && $licenseKey === null) {
            return null;
        }

        if ($email === null || $licenseKey === null) {
            throw ValidationException::withMessages([
                'credentials' => 'Both email and plugin_license_key are required when authenticating.',
            ]);
        }

        $user = User::query()
            ->where('email', $email)
            ->where('plugin_license_key', $licenseKey)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'credentials' => 'Invalid credentials. The provided email or plugin license key is incorrect.',
            ]);
        }

        return $user;
    }

    /**
     * Search approved, active marketplace plugins.
     *
     * @return list<array<string, mixed>>
     */
    public function search(string $query, ?string $type = null, int $limit = self::DEFAULT_LIMIT, ?User $user = null): array
    {
        $limit = min(max(1, $limit), self::MAX_LIMIT);
        $type = $this->sanitizeType($type);
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $plugins = $this->publicDirectoryQuery()
            ->with(['prices' => fn ($q) => $q->active()])
            ->when($type !== null, fn (Builder $q) => $q->where('type', $type))
            ->where(function (Builder $q) use ($query): void {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderByDesc('featured')
            ->latest()
            ->limit(max($limit * 5, 50))
            ->get();

        return $plugins
            ->sort(function (Plugin $a, Plugin $b) use ($query): int {
                $scoreCompare = $this->matchScore($b, $query) <=> $this->matchScore($a, $query);
                if ($scoreCompare !== 0) {
                    return $scoreCompare;
                }

                $featuredCompare = ((int) $b->featured) <=> ((int) $a->featured);
                if ($featuredCompare !== 0) {
                    return $featuredCompare;
                }

                return $b->created_at <=> $a->created_at;
            })
            ->take($limit)
            ->values()
            ->map(fn (Plugin $plugin) => $this->toSummary($plugin, $user))
            ->all();
    }

    /**
     * Fetch one publicly visible marketplace plugin by composer name.
     *
     * @return array<string, mixed>|null
     */
    public function getByName(string $name, ?User $user = null): ?array
    {
        $name = trim($name);

        if ($name === '' || ! str_contains($name, '/')) {
            return null;
        }

        [$vendor, $package] = array_pad(explode('/', $name, 2), 2, '');

        return $this->getByVendorPackage($vendor, $package, $user);
    }

    /**
     * Fetch one publicly visible marketplace plugin by vendor/package path.
     *
     * @return array<string, mixed>|null
     */
    public function getByVendorPackage(string $vendor, string $package, ?User $user = null): ?array
    {
        $vendor = $this->sanitizePathSegment($vendor);
        $package = $this->sanitizePathSegment($package);

        if ($vendor === null || $package === null) {
            return null;
        }

        $plugin = $this->publicDirectoryQuery()
            ->with(['prices' => fn ($q) => $q->active()])
            ->where('name', "{$vendor}/{$package}")
            ->first();

        if (! $plugin) {
            return null;
        }

        return $this->toDetail($plugin, $user);
    }

    /**
     * Same visibility as the public marketplace directory: approved + active.
     *
     * @return Builder<Plugin>
     */
    protected function publicDirectoryQuery(): Builder
    {
        return Plugin::query()->approved();
    }

    /**
     * @return array<string, mixed>
     */
    protected function toSummary(Plugin $plugin, ?User $user = null): array
    {
        $hasAccess = $user ? $user->hasPluginAccess($plugin) : null;

        return [
            'name' => $plugin->name,
            'description' => $plugin->description,
            'type' => $plugin->type?->value ?? (string) $plugin->type,
            'price' => $this->formatPublicPrice($plugin),
            'your_price' => $user ? $this->formatBestPriceForUser($plugin, $user) : null,
            'has_access' => $hasAccess,
            'access_label' => $this->accessLabel($plugin, $hasAccess),
            'featured' => (bool) $plugin->featured,
            'is_official' => $plugin->isOfficial(),
            'works_in_jump' => $plugin->worksInJump(),
            'latest_version' => $plugin->latest_version,
            'marketplace_url' => route('plugins.show', $plugin->routeParams(), absolute: true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function toDetail(Plugin $plugin, ?User $user = null): array
    {
        return array_merge($this->toSummary($plugin, $user), [
            'repository_url' => $plugin->getGithubUrl(),
            'packagist_url' => $plugin->isFree() ? $plugin->getPackagistUrl() : null,
        ]);
    }

    protected function accessLabel(Plugin $plugin, ?bool $hasAccess): ?string
    {
        if ($hasAccess === null) {
            return null;
        }

        if ($plugin->isFree()) {
            return $hasAccess ? 'Free — available to install' : null;
        }

        if ($hasAccess) {
            return 'You already have access';
        }

        return 'Purchase required';
    }

    protected function formatPublicPrice(Plugin $plugin): ?string
    {
        if ($plugin->isFree()) {
            return null;
        }

        $regular = $plugin->prices->first(fn ($price) => $price->tier === PriceTier::Regular)
            ?? $plugin->getRegularPrice();

        if (! $regular) {
            return null;
        }

        return '$'.$regular->formatted_amount;
    }

    protected function formatBestPriceForUser(Plugin $plugin, User $user): ?string
    {
        if ($plugin->isFree() || $user->hasPluginAccess($plugin)) {
            return null;
        }

        $best = $plugin->getBestPriceForUser($user);
        $regular = $plugin->getRegularPrice();

        if (! $best) {
            return null;
        }

        if ($regular && $best->id !== $regular->id) {
            return '$'.$best->formatted_amount.' (subscriber)';
        }

        return '$'.$best->formatted_amount;
    }

    protected function matchScore(Plugin $plugin, string $query): int
    {
        $queryLower = mb_strtolower($query);
        $name = mb_strtolower((string) $plugin->name);
        $description = mb_strtolower((string) $plugin->description);

        $score = 0;

        if ($name === $queryLower) {
            $score += 100;
        } elseif (str_contains($name, $queryLower)) {
            $score += 50;
        }

        if (str_contains($description, $queryLower)) {
            $score += 10;
        }

        return $score;
    }

    protected function sanitizeType(?string $type): ?string
    {
        if ($type === null || $type === '') {
            return null;
        }

        return PluginType::tryFrom($type)?->value;
    }

    protected function sanitizePathSegment(string $segment): ?string
    {
        $segment = trim($segment);

        if ($segment === '' || str_contains($segment, '..') || str_contains($segment, '/') || str_contains($segment, '\\')) {
            return null;
        }

        if (! preg_match('/^[A-Za-z0-9_.-]+$/', $segment)) {
            return null;
        }

        return $segment;
    }
}
