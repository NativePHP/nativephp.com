<?php

namespace App\Mcp\Tools\Admin;

use App\Enums\PluginStatus;
use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\Plugin;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-search-plugins')]
#[Description('Search/list plugins including pending/draft/rejected. Read-only; no secrets.')]
#[IsReadOnly]
class AdminSearchPlugins extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:draft,pending,approved,rejected,all'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 25);
        $status = $validated['status'] ?? 'all';

        $query = Plugin::query()
            ->with('user:id,name,email')
            ->latest('id');

        if ($status !== 'all') {
            $query->where('status', PluginStatus::from($status));
        }

        if (! empty($validated['query'])) {
            $like = '%'.$validated['query'].'%';
            $query->where(function ($builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        $plugins = $query->limit($limit)->get()->map(fn (Plugin $plugin): array => [
            'id' => $plugin->id,
            'name' => $plugin->name,
            'status' => $plugin->status?->value,
            'is_official' => (bool) $plugin->is_official,
            'is_featured' => (bool) ($plugin->featured ?? false),
            'developer_email' => $plugin->user?->email,
            'created_at' => optional($plugin->created_at)?->toIso8601String(),
            'updated_at' => optional($plugin->updated_at)?->toIso8601String(),
        ])->all();

        return Response::text($this->toJson([
            'status' => $status,
            'query' => $validated['query'] ?? null,
            'count' => count($plugins),
            'plugins' => $plugins,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Optional name/description search.'),
            'status' => $schema->string()->description('draft|pending|approved|rejected|all (default all).'),
            'limit' => $schema->integer()->description('Max results (default 25, max 100).'),
        ];
    }
}
