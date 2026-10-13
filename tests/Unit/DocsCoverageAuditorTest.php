<?php

namespace Tests\Unit;

use App\Enums\DocsPlatform;
use App\Services\DocsCoverageAuditor;
use App\Services\DocsVersionRegistry;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsCoverageAuditorTest extends TestCase
{
    private const FAKE_DOCS_TEXT = 'Set the deeplink_scheme option, then run native:validate before you build.';

    #[Test]
    public function it_flags_a_config_key_never_mentioned_in_the_docs(): void
    {
        $this->fakeMobileRepo(
            configPhp: "<?php\nreturn [\n    'deeplink_scheme' => env('NATIVEPHP_DEEPLINK_SCHEME'),\n    'undocumented_key' => true,\n];\n"
        );

        $results = $this->auditor()->audit(DocsPlatform::Mobile);

        $documented = $results->firstWhere('identifier', 'deeplink_scheme');
        $undocumented = $results->firstWhere('identifier', 'undocumented_key');

        $this->assertTrue($documented['documented']);
        $this->assertFalse($undocumented['documented']);
    }

    #[Test]
    public function it_only_matches_nested_config_keys_up_to_one_level_deep(): void
    {
        $this->fakeMobileRepo(
            configPhp: "<?php\nreturn [\n    'android' => [\n        'permissions' => [\n            'too_deep_to_flag' => 'a description',\n        ],\n    ],\n];\n"
        );

        $results = $this->auditor()->audit(DocsPlatform::Mobile);

        $this->assertNull($results->firstWhere('identifier', 'too_deep_to_flag'));
    }

    #[Test]
    public function it_extracts_the_command_name_from_either_quote_style(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::response(['default_branch' => 'main']),
            'api.github.com/repos/nativephp/mobile-air/git/trees/main*' => Http::response([
                'tree' => [
                    ['path' => 'src/Commands/ValidateCommand.php', 'type' => 'blob', 'sha' => 'sha-single'],
                    ['path' => 'src/Commands/WatchCommand.php', 'type' => 'blob', 'sha' => 'sha-double'],
                ],
                'truncated' => false,
            ]),
            'api.github.com/repos/nativephp/mobile-air/git/blobs/sha-single' => Http::response([
                'content' => base64_encode("<?php\nclass ValidateCommand {\n    protected \$signature = 'native:validate';\n}\n"),
            ]),
            'api.github.com/repos/nativephp/mobile-air/git/blobs/sha-double' => Http::response([
                'content' => base64_encode('<?php class WatchCommand { protected $signature = "native:watch"; }'),
            ]),
        ]);

        $results = $this->auditor()->audit(DocsPlatform::Mobile);

        $this->assertNotNull($results->firstWhere('identifier', 'native:validate'));
        $this->assertNotNull($results->firstWhere('identifier', 'native:watch'));
    }

    #[Test]
    public function it_ignores_files_outside_a_commands_directory(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::response(['default_branch' => 'main']),
            'api.github.com/repos/nativephp/mobile-air/git/trees/main*' => Http::response([
                'tree' => [
                    ['path' => 'src/Support/Helper.php', 'type' => 'blob', 'sha' => 'sha-helper'],
                ],
                'truncated' => false,
            ]),
        ]);

        $results = $this->auditor()->audit(DocsPlatform::Mobile);

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'sha-helper'));
        $this->assertTrue($results->isEmpty());
    }

    #[Test]
    public function it_returns_an_empty_collection_when_the_config_file_is_missing_from_the_tree(): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::response(['default_branch' => 'main']),
            'api.github.com/repos/nativephp/mobile-air/git/trees/main*' => Http::response(['tree' => [], 'truncated' => false]),
        ]);

        $this->assertTrue($this->auditor()->audit(DocsPlatform::Mobile)->isEmpty());
    }

    private function fakeMobileRepo(string $configPhp): void
    {
        Http::fake([
            'api.github.com/repos/nativephp/mobile-air' => Http::response(['default_branch' => 'main']),
            'api.github.com/repos/nativephp/mobile-air/git/trees/main*' => Http::response([
                'tree' => [
                    ['path' => 'config/nativephp.php', 'type' => 'blob', 'sha' => 'sha-config'],
                ],
                'truncated' => false,
            ]),
            'api.github.com/repos/nativephp/mobile-air/git/blobs/sha-config' => Http::response([
                'content' => base64_encode($configPhp),
            ]),
        ]);
    }

    private function auditor(string $docsText = self::FAKE_DOCS_TEXT): DocsCoverageAuditor
    {
        return new class(app(DocsVersionRegistry::class), $docsText) extends DocsCoverageAuditor
        {
            public function __construct(DocsVersionRegistry $registry, private readonly string $fakeDocsText)
            {
                parent::__construct($registry);
            }

            protected function docsText(DocsPlatform $platform): string
            {
                return $this->fakeDocsText;
            }
        };
    }
}
