<?php

namespace Tests\Feature\SatisSync;

use App\Jobs\SyncPluginReleases;
use App\Models\GitHubInstallation;
use App\Models\Plugin;
use App\Models\User;
use App\Services\SatisService;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\InteractsWithGitHubApp;
use Tests\TestCase;

class SatisBuildTokenTest extends TestCase
{
    use InteractsWithGitHubApp;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureGitHubApp();

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
        GitHubInstallation::factory()->for($user)->create(['account_login' => 'acme']);

        $plugin = $this->paidPluginFor($user);

        Http::fake([
            'api.github.com/app/installations/*/access_tokens' => $this->mintedToken('ghs_installation'),
            'api.github.com/repos/acme/camera-plugin' => fn (Request $request) => $request->hasHeader('Authorization', 'Bearer ghs_installation')
                ? Http::response(['message' => 'Not Found'], 404)
                : Http::response(['full_name' => 'acme/camera-plugin']),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghu_owner');
    }

    public function test_the_build_mints_a_new_installation_token_instead_of_using_the_cached_one(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->for($user)->create(['account_login' => 'acme']);
        Cache::put("github_installation_token_{$installation->installation_id}", 'ghs_nearly_expired', now()->addMinutes(55));

        $plugin = $this->paidPluginFor($user);

        Http::fake([
            'api.github.com/app/installations/*/access_tokens' => $this->mintedToken('ghs_fresh'),
            'api.github.com/repos/acme/camera-plugin' => Http::response(['full_name' => 'acme/camera-plugin']),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghs_fresh');
        $this->assertSame('ghs_fresh', Cache::get("github_installation_token_{$installation->installation_id}"));
    }

    public function test_the_build_falls_back_to_the_owners_token_when_minting_fails(): void
    {
        $user = User::factory()->withGitHubApp()->create(['github_token' => encrypt('ghu_owner')]);
        GitHubInstallation::factory()->for($user)->create(['account_login' => 'acme']);

        $plugin = $this->paidPluginFor($user);

        Http::fake([
            'api.github.com/app/installations/*/access_tokens' => Http::response(['message' => 'Server Error'], 502),
            'api.github.com/repos/acme/camera-plugin' => Http::response(['full_name' => 'acme/camera-plugin']),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SatisService)->buildForPlugin($plugin);

        $this->assertSatisBuiltWithToken('ghu_owner');
    }

    public function test_a_release_sync_that_builds_mints_a_new_installation_token(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->for($user)->create(['account_login' => 'acme']);
        Cache::put("github_installation_token_{$installation->installation_id}", 'ghs_nearly_expired', now()->addMinutes(55));

        $plugin = $this->paidPluginFor($user);

        Http::fake([
            'api.github.com/app/installations/*/access_tokens' => $this->mintedToken('ghs_fresh'),
            'api.github.com/repos/acme/camera-plugin' => Http::response(['full_name' => 'acme/camera-plugin']),
            'api.github.com/repos/acme/camera-plugin/releases*' => Http::response([]),
            'satis.test/*' => Http::response(['job_id' => 'test-123'], 202),
        ]);

        (new SyncPluginReleases($plugin))->handle(new SatisService);

        $this->assertSatisBuiltWithToken('ghs_fresh');
    }

    public function test_a_release_sync_that_will_not_build_keeps_using_the_cached_token(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->for($user)->create(['account_login' => 'acme']);
        Cache::put("github_installation_token_{$installation->installation_id}", 'ghs_cached', now()->addMinutes(55));

        $plugin = $this->paidPluginFor($user);

        Http::fake([
            'api.github.com/repos/acme/camera-plugin' => Http::response(['full_name' => 'acme/camera-plugin']),
            'api.github.com/repos/acme/camera-plugin/releases*' => Http::response([]),
        ]);

        (new SyncPluginReleases($plugin, triggerSatisBuild: false))->handle(new SatisService);

        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/releases')
            && $request->hasHeader('Authorization', 'Bearer ghs_cached'));
        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/access_tokens'));
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

    private function mintedToken(string $token): PromiseInterface
    {
        return Http::response(['token' => $token, 'expires_at' => now()->addHour()->toIso8601String()], 201);
    }

    private function assertSatisBuiltWithToken(string $token): void
    {
        Http::assertSent(fn (Request $request) => $request->url() === 'https://satis.test/api/build'
            && $request['github_token'] === $token);
    }
}
