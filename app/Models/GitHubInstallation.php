<?php

namespace App\Models;

use Database\Factories\GitHubInstallationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GitHubInstallation extends Model
{
    /** @use HasFactory<GitHubInstallationFactory> */
    use HasFactory;

    protected $table = 'github_installations';

    protected $guarded = [];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTokenExpired(): bool
    {
        if (! $this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->isPast();
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function hasAccessToRepo(string $owner, string $repo): bool
    {
        if ($this->isSuspended()) {
            return false;
        }

        // Account login must match the repo owner
        if (strtolower($this->account_login) !== strtolower($owner)) {
            return false;
        }

        // If "all" repos are selected, grant access
        if ($this->selection_type === 'all') {
            return true;
        }

        // Check if the specific repo is in the selected list
        $selectedRepos = $this->repository_selection ?? [];

        return in_array("{$owner}/{$repo}", $selectedRepos, true);
    }

    public function getAccessToken(): ?string
    {
        if (! $this->access_token) {
            return null;
        }

        try {
            return decrypt($this->access_token);
        } catch (\Exception) {
            return null;
        }
    }

    protected function casts(): array
    {
        return [
            'repository_selection' => 'array',
            'token_expires_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }
}
