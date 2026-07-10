<?php

namespace Tests\Feature;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentationRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Doc pages contain fenced code blocks. Torchlight throws outside
        // production when no token is configured (as in CI), so give it a token
        // and fake the API to force its offline fallback.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    #[Test]
    public function text_page_shows_example_blade_expression_literally_instead_of_evaluating_it(): void
    {
        $this->withoutVite()
            ->get('/docs/mobile/4/edge-components/text')
            ->assertOk()
            ->assertSee('{{ $variable }}');
    }

    #[Test]
    public function every_documentation_page_renders_without_evaluating_example_variables(): void
    {
        /** @var ViewFactory $factory */
        $factory = resolve(ViewFactory::class);
        $factory->addExtension('md', 'blade');

        $base = resource_path('views/docs');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        $failures = [];

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'md' || str_starts_with($file->getBasename(), '_')) {
                continue;
            }

            $view = 'docs/'.substr($file->getPathname(), strlen($base) + 1, -3);

            try {
                $factory->make($view, ['user' => null])->render();
            } catch (\Throwable $e) {
                $failures[$view] = $e->getMessage();
            }
        }

        $this->assertSame(
            [],
            $failures,
            'Documentation pages must escape example Blade expressions with @{{ }} or @verbatim. '
            .'These pages threw while rendering: '.json_encode($failures, JSON_PRETTY_PRINT),
        );
    }
}
