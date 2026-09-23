<?php

namespace App\Services;

use App\Models\User;
use App\Support\ConsumerEmailDomains;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CompanyAggregator
{
    /**
     * @return Collection<string, array{domain: string, users_count: int, earliest_signup: mixed, latest_signup: mixed}>
     */
    public function aggregate(): Collection
    {
        $domainExpression = $this->domainExpression();

        $rows = User::query()
            ->select([
                DB::raw("{$domainExpression} as domain"),
                DB::raw('COUNT(*) as users_count'),
                DB::raw('MIN(created_at) as earliest_signup'),
                DB::raw('MAX(created_at) as latest_signup'),
            ])
            ->whereNotNull('email')
            ->where('email', 'like', '%@%')
            ->groupBy(DB::raw($domainExpression))
            ->get();

        return $rows
            ->filter(fn ($row): bool => ConsumerEmailDomains::isCompanyDomain($row->domain))
            ->mapWithKeys(fn ($row): array => [
                $row->domain => [
                    'domain' => $row->domain,
                    'users_count' => (int) $row->users_count,
                    'earliest_signup' => $row->earliest_signup,
                    'latest_signup' => $row->latest_signup,
                ],
            ]);
    }

    /**
     * @return Collection<int, User>
     */
    public function usersForDomain(string $domain): Collection
    {
        $domain = strtolower($domain);
        $domainExpression = $this->domainExpression();

        return User::query()
            ->whereRaw("{$domainExpression} = ?", [$domain])
            ->orderBy('created_at')
            ->get();
    }

    public function countUsersForDomain(string $domain): int
    {
        $domain = strtolower($domain);
        $domainExpression = $this->domainExpression();

        return User::query()
            ->whereRaw("{$domainExpression} = ?", [$domain])
            ->count();
    }

    protected function domainExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "lower(substr(email, instr(email, '@') + 1))",
            default => "LOWER(SUBSTRING_INDEX(email, '@', -1))",
        };
    }
}
