<?php

namespace Tests\Feature\SatisSync;

use App\Jobs\SyncPluginReleases;
use App\Models\GitHubInstallation;
use App\Models\Plugin;
use App\Models\User;
use App\Services\SatisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SatisBuildTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.satis.url' => 'https://satis.test',
            'services.satis.api_key' => 'test-key',
            'services.github.token' => 'ghp_platform',
        ]);
    }

    public function test_the_build_gets_the_owners_token_when_github_accepts_it(): void
    {
        $plugin = $this->paidPluginFor(User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_owner')]));

        Http::fake([
            'api.github.com/repos/acme/camera-plugin' => Http::response(['full_name' => 'acme/camera-plugin']),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghu_owner');
    }

    public function test_the_build_skips_an_owner_token_github_has_revoked(): void
    {
        $plugin = $this->paidPluginFor(User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_revoked')]));

        Http::fake([
            'api.github.com/repos/acme/camera-plugin' => Http::response(['message' => 'Bad credentials'], 401),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghp_platform');
    }

    public function test_the_build_skips_an_installation_token_that_cannot_see_the_repository(): void
    {
        $user = User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_owner')]);
        $installation = GitHubInstallation::factory()->for($user)->create(['account_login' => 'acme']);
        Cache::put("github_installation_token_{$installation->installation_id}", 'ghs_installation', now()->addMinutes(55));

        $plugin = $this->paidPluginFor($user);

        Http::fake([
            'api.github.com/repos/acme/camera-plugin' => fn (Request $request) => $request->hasHeader('Authorization', 'Bearer ghs_installation')
                ? Http::response(['message' => 'Not Found'], 404)
                : Http::response(['full_name' => 'acme/camera-plugin']),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghu_owner');
    }

    public function test_the_build_keeps_the_owners_token_when_github_is_having_trouble(): void
    {
        $plugin = $this->paidPluginFor(User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_owner')]));

        Http::fake([
            'api.github.com/repos/acme/camera-plugin' => Http::response(['message' => 'Server Error'], 502),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghu_owner');
    }

    public function test_a_release_sync_builds_with_the_token_it_fetched_releases_with(): void
    {
        $plugin = $this->paidPluginFor(User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_revoked')]));

        Http::fake([
            'api.github.com/repos/acme/camera-plugin' => Http::response(['message' => 'Bad credentials'], 401),
            'api.github.com/repos/acme/camera-plugin/releases*' => Http::response([]),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SyncPluginReleases($plugin))->handle(new SatisService);

        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/releases')
            && $request->hasHeader('Authorization', 'Bearer ghp_platform'));

        $this->assertSatisBuiltWithToken('ghp_platform');
    }

    private function paidPluginFor(User $user): Plugin
    {
        return Plugin::factory()->paid()->approved()->for($user)->create([
            'repository_url' => 'https://github.com/acme/camera-plugin',
        ]);
    }

    private function assertSatisBuiltWithToken(string $token): void
    {
        Http::assertSent(fn (Request $request) => $request->url() === 'https://satis.test/api/build'
            && $request['github_token'] === $token);
    }
}
