<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Services\CompanyAggregator;
use App\Support\ConsumerEmailDomains;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-get-company')]
#[Description('Get a company domain rollup and its users (name/email/created_at).')]
#[IsReadOnly]
class AdminGetCompany extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $domain = strtolower(trim($validated['domain']));

        if (! ConsumerEmailDomains::isCompanyDomain($domain)) {
            return Response::error("Domain [{$domain}] is treated as a consumer mailbox, not a company.");
        }

        $aggregator = app(CompanyAggregator::class);
        $users = $aggregator->usersForDomain($domain);

        if ($users->isEmpty()) {
            return Response::error("No users found for domain [{$domain}].");
        }

        return Response::text($this->toJson([
            'domain' => $domain,
            'users_count' => $users->count(),
            'earliest_signup' => optional($users->min('created_at'))?->toIso8601String(),
            'latest_signup' => optional($users->max('created_at'))?->toIso8601String(),
            'users' => $users->map(fn ($user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => optional($user->created_at)?->toIso8601String(),
            ])->values()->all(),
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'domain' => $schema->string()->description('Company email domain, e.g. acme.com.')->required(),
        ];
    }
}
