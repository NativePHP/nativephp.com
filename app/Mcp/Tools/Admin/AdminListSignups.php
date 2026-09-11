<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-list-signups')]
#[Description('List users created in a date window. Defaults to today in America/New_York. Returns name, email, company domain (from email), and created_at.')]
#[IsReadOnly]
class AdminListSignups extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $tz = 'America/New_York';
        $limit = (int) ($validated['limit'] ?? 100);

        if (! empty($validated['from']) || ! empty($validated['to'])) {
            $from = isset($validated['from'])
                ? Carbon::parse($validated['from'], $tz)->startOfDay()->utc()
                : Carbon::now($tz)->startOfDay()->utc();
            $to = isset($validated['to'])
                ? Carbon::parse($validated['to'], $tz)->endOfDay()->utc()
                : Carbon::now($tz)->endOfDay()->utc();
        } else {
            $day = Carbon::parse($validated['date'] ?? 'today', $tz);
            $from = $day->copy()->startOfDay()->utc();
            $to = $day->copy()->endOfDay()->utc();
        }

        $users = User::query()
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_domain' => $this->domainFromEmail($user->email),
                'created_at' => $user->created_at?->timezone($tz)->toIso8601String(),
            ])
            ->all();

        return Response::text($this->toJson([
            'timezone' => $tz,
            'from' => $from->timezone($tz)->toIso8601String(),
            'to' => $to->timezone($tz)->toIso8601String(),
            'count' => count($users),
            'users' => $users,
        ]));
    }

    protected function domainFromEmail(?string $email): ?string
    {
        if (! is_string($email) || ! str_contains($email, '@')) {
            return null;
        }

        return strtolower(substr($email, strrpos($email, '@') + 1));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'date' => $schema->string()->description('Single calendar day in America/New_York (YYYY-MM-DD). Defaults to today.'),
            'from' => $schema->string()->description('Optional range start day (America/New_York).'),
            'to' => $schema->string()->description('Optional range end day (America/New_York).'),
            'limit' => $schema->integer()->description('Max users (default 100, max 200).'),
        ];
    }
}
