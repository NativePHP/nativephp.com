<?php

namespace Tests\Feature;

use App\Support\GitHub;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GitHubRepositoryStarsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    private function fakeStars(int $mobileAir = 1234, int $desktop = 5678): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::response(['stargazers_count' => $mobileAir]),
            'api.github.com/repos/nativephp/desktop' => Http::response(['stargazers_count' => $desktop]),
        ]);
    }

    public function test_command_fetches_and_caches_star_counts_for_both_repositories(): void
    {
        $this->fakeStars();

        $this->artisan('github:sync-repo-stars')->assertSuccessful();

        $this->assertSame(1234, GitHub::mobileAir()->stars());
        $this->assertSame(5678, GitHub::desktop()->stars());

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.github.com/repos/nativephp/mobile-air');
        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.github.com/repos/nativephp/desktop');
        Http::assertSentCount(2);
    }

    public function test_command_is_scheduled_daily(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event) => str_contains($event->command, 'github:sync-repo-stars'));

        $this->assertNotNull($event);
        $this->assertSame('0 9 * * *', $event->expression);
    }

    public function test_request_uses_the_configured_github_token(): void
    {
        config(['services.github.token' => 'test-token']);
        $this->fakeStars();

        GitHub::mobileAir()->refreshStars();

        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_request_is_sent_without_a_token_when_none_is_configured(): void
    {
        config(['services.github.token' => null]);
        $this->fakeStars();

        GitHub::mobileAir()->refreshStars();

        Http::assertSent(fn (Request $request) => ! $request->hasHeader('Authorization'));
    }

    public function test_failed_api_call_keeps_the_previously_cached_count(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::sequence()
                ->push(['stargazers_count' => 900])
                ->push(['message' => 'API rate limit exceeded'], 403),
            'api.github.com/repos/nativephp/desktop' => Http::sequence()
                ->push(['stargazers_count' => 400])
                ->pushFailedConnection(),
        ]);

        $this->artisan('github:sync-repo-stars')->assertSuccessful();
        $this->artisan('github:sync-repo-stars')->assertFailed();

        $this->assertSame(900, GitHub::mobileAir()->stars());
        $this->assertSame(400, GitHub::desktop()->stars());
    }

    public function test_response_without_a_star_count_keeps_the_previously_cached_count(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::sequence()
                ->push(['stargazers_count' => 900])
                ->push(['unexpected' => 'shape']),
        ]);

        GitHub::mobileAir()->refreshStars();

        $this->assertNull(GitHub::mobileAir()->refreshStars());
        $this->assertSame(900, GitHub::mobileAir()->stars());
    }

    public function test_stars_are_null_when_nothing_has_been_cached(): void
    {
        Http::fake();

        $this->assertNull(GitHub::mobileAir()->stars());
        $this->assertNull(GitHub::mobileAir()->formattedStars());

        Http::assertNothingSent();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function starCountProvider(): array
    {
        return [
            'zero' => [0, '0 stars'],
            'one' => [1, '1 star'],
            'hundreds' => [950, '950 stars'],
            'exact thousand' => [1000, '1k stars'],
            'thousands' => [1234, '1.2k stars'],
            'tens of thousands' => [12345, '12.3k stars'],
        ];
    }

    #[DataProvider('starCountProvider')]
    public function test_star_counts_are_formatted_compactly(int $count, string $expected): void
    {
        Http::fake(['api.github.com/repos/nativephp/mobile-air' => Http::response(['stargazers_count' => $count])]);

        GitHub::mobileAir()->refreshStars();

        $this->assertSame($expected, GitHub::mobileAir()->formattedStars());
    }

    public function test_dropdowns_show_repository_names_and_cached_star_counts(): void
    {
        $this->fakeStars();
        $this->artisan('github:sync-repo-stars');

        $this->blade('<x-navbar.device-dropdowns />')
            ->assertSeeInOrder(['https://github.com/nativephp/mobile-air', 'mobile-air', '1.2k stars'], false)
            ->assertSeeInOrder(['https://github.com/nativephp/desktop', 'desktop', '5.7k stars'], false)
            ->assertDontSee('Visit our GitHub repository');
    }

    public function test_dropdowns_fall_back_to_plain_text_when_no_star_counts_are_cached(): void
    {
        Http::fake();

        $this->blade('<x-navbar.device-dropdowns />')
            ->assertSeeInOrder(['https://github.com/nativephp/mobile-air', 'mobile-air', 'GitHub'], false)
            ->assertSeeInOrder(['https://github.com/nativephp/desktop', 'desktop', 'GitHub'], false)
            ->assertDontSee('stars');

        Http::assertNothingSent();
    }

    public function test_rendering_the_dropdowns_never_calls_the_github_api(): void
    {
        $this->fakeStars();
        $this->artisan('github:sync-repo-stars');
        Http::fake();

        $this->blade('<x-navbar.device-dropdowns />')->assertSee('1.2k stars');

        Http::assertNothingSent();
    }
}
