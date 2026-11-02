<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-search-users')]
#[Description('Search users by email or name substring.')]
#[IsReadOnly]
class AdminSearchUsers extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $like = '%'.$validated['query'].'%';
        $limit = (int) ($validated['limit'] ?? 20);

        $users = User::query()
            ->where(function ($builder) use ($like): void {
                $builder->where('email', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('display_name', 'like', $like);
            })
            ->limit($limit)
            ->get(['id', 'name', 'display_name', 'email', 'created_at'])
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'display_name' => $user->display_name,
                'email' => $user->email,
                'created_at' => $user->created_at?->toIso8601String(),
            ])
            ->all();

        return Response::text($this->toJson([
            'query' => $validated['query'],
            'count' => count($users),
            'users' => $users,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Email or name substring.')->required(),
            'limit' => $schema->integer()->description('Max results (default 20, max 50).'),
        ];
    }
}
