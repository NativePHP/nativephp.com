<?php

namespace Tests\Feature\Console;

use App\Enums\DocsCoverageCategory;
use App\Enums\DocsPlatform;
use App\Models\DocsCoverageGap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditDocsCoverageCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_records_a_gap_for_each_platform_by_default(): void
    {
        $this->fakeRepo('nativephp/mobile-air', "<?php\nreturn [\n    'undocumented_key' => true,\n];\n");
        $this->fakeRepo('nativephp/desktop', "<?php\nreturn [\n    'undocumented_key' => true,\n];\n");

        $this->artisan('docs:audit-coverage')->assertSuccessful();

        $this->assertSame(
            [DocsPlatform::Desktop, DocsPlatform::Mobile],
            DocsCoverageGap::query()->orderBy('platform')->pluck('platform')->all()
        );
    }

    #[Test]
    public function it_only_audits_the_given_platform(): void
    {
        $this->fakeRepo('nativephp/mobile-air', "<?php\nreturn [\n    'undocumented_key' => true,\n];\n");

        $this->artisan('docs:audit-coverage', ['platform' => 'mobile'])->assertSuccessful();

        $this->assertSame(1, DocsCoverageGap::query()->count());
        $this->assertSame(DocsPlatform::Mobile, DocsCoverageGap::query()->sole()->platform);
    }

    #[Test]
    public function it_fails_for_an_unknown_platform(): void
    {
        $this->artisan('docs:audit-coverage', ['platform' => 'blackberry'])->assertFailed();
    }

    #[Test]
    public function it_removes_gaps_for_identifiers_no_longer_present_in_source(): void
    {
        DocsCoverageGap::factory()->create([
            'platform' => DocsPlatform::Mobile,
            'category' => DocsCoverageCategory::ConfigKey,
            'identifier' => 'removed_key',
        ]);

        $this->fakeRepo('nativephp/mobile-air', "<?php\nreturn [\n    'current_key' => true,\n];\n");

        $this->artisan('docs:audit-coverage', ['platform' => 'mobile'])->assertSuccessful();

        $this->assertNull(DocsCoverageGap::query()->where('identifier', 'removed_key')->first());
        $this->assertNotNull(DocsCoverageGap::query()->where('identifier', 'current_key')->first());
    }

    #[Test]
    public function rerunning_it_updates_an_existing_gap_instead_of_duplicating_it(): void
    {
        $this->fakeRepo('nativephp/mobile-air', "<?php\nreturn [\n    'a_key' => true,\n];\n");

        $this->artisan('docs:audit-coverage', ['platform' => 'mobile'])->assertSuccessful();
        $this->artisan('docs:audit-coverage', ['platform' => 'mobile'])->assertSuccessful();

        $this->assertSame(1, DocsCoverageGap::query()->where('identifier', 'a_key')->count());
    }

    private function fakeRepo(string $package, string $configPhp): void
    {
        Http::fake([
            "api.github.com/repos/{$package}" => Http::response(['default_branch' => 'main']),
            "api.github.com/repos/{$package}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'config/nativephp.php', 'type' => 'blob', 'sha' => 'sha-config'],
                ],
                'truncated' => false,
            ]),
            "api.github.com/repos/{$package}/git/blobs/sha-config" => Http::response([
                'content' => base64_encode($configPhp),
            ]),
        ]);
    }
}
