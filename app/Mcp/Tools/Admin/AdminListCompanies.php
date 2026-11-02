<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Services\CompanyAggregator;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-list-companies')]
#[Description('List company email-domain rollups with user counts (consumer mailboxes excluded).')]
#[IsReadOnly]
class AdminListCompanies extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
            'min_users' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $limit = (int) ($validated['limit'] ?? 50);
        $minUsers = (int) ($validated['min_users'] ?? 1);

        $companies = app(CompanyAggregator::class)->aggregate()
            ->filter(fn (array $row): bool => $row['users_count'] >= $minUsers)
            ->sortByDesc('users_count')
            ->take($limit)
            ->values()
            ->all();

        return Response::text($this->toJson([
            'count' => count($companies),
            'companies' => $companies,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()->description('Max companies (default 50, max 200).'),
            'min_users' => $schema->integer()->description('Minimum users_count (default 1).'),
        ];
    }
}
