<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubUserService
{
    public function __construct(
        private User $user
    ) {}

    public static function for(User $user): static
    {
        return new static($user);
    }

    public function getRepositories(bool $includePrivate = true): Collection
    {
        $token = $this->user->getGitHubToken();

        if (! $token) {
            return collect();
        }

        $cacheKey = "github_repos_{$this->user->id}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($token, $includePrivate) {
            return $this->fetchRepositories($token, $includePrivate);
        });
    }

    public function clearRepositoryCache(): void
    {
        Cache::forget("github_repos_{$this->user->id}");
    }

    protected function fetchRepositories(string $token, bool $includePrivate): Collection
    {
        $repos = collect();
        $page = 1;
        $perPage = 100;

        do {
            $response = Http::withToken($token)
                ->get('https://api.github.com/user/repos', [
                    'per_page' => $perPage,
                    'page' => $page,
                    'sort' => 'updated',
                    'direction' => 'desc',
                    'affiliation' => 'owner,collaborator,organization_member',
                ]);

            if ($response->failed()) {
                Log::warning('Failed to fetch GitHub repos', [
                    'user_id' => $this->user->id,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                break;
            }

            $pageRepos = collect($response->json());
            $repos = $repos->concat($pageRepos);
            $page++;
        } while ($pageRepos->count() === $perPage && $page <= 10);

        if (! $includePrivate) {
            $repos = $repos->where('private', false);
        }

        return $repos->map(function ($repo) {
            return [
                'id' => $repo['id'],
                'name' => $repo['name'],
                'full_name' => $repo['full_name'],
                'private' => $repo['private'],
                'html_url' => $repo['html_url'],
                'description' => $repo['description'],
                'default_branch' => $repo['default_branch'],
                'pushed_at' => $repo['pushed_at'],
            ];
        })->values();
    }

    public function getRepository(string $owner, string $repo): ?array
    {
        $token = $this->user->getGitHubToken();

        if (! $token) {
            return null;
        }

        $response = Http::withToken($token)
            ->get("https://api.github.com/repos/{$owner}/{$repo}");

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'full_name' => $data['full_name'],
            'private' => $data['private'],
            'html_url' => $data['html_url'],
            'description' => $data['description'],
            'default_branch' => $data['default_branch'],
        ];
    }

    public function getComposerJson(string $owner, string $repo, string $branch = 'main'): ?array
    {
        $token = $this->user->getGitHubToken();

        if (! $token) {
            return null;
        }

        $response = Http::withToken($token)
            ->get("https://api.github.com/repos/{$owner}/{$repo}/contents/composer.json", [
                'ref' => $branch,
            ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (! isset($data['content'])) {
            return null;
        }

        $content = base64_decode($data['content']);

        return json_decode($content, true);
    }
}
