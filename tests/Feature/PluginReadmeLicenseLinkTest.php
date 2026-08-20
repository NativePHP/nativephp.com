<?php

namespace Tests\Feature;

use App\Features\ShowPlugins;
use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PluginReadmeLicenseLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowPlugins::class, true);
    }

    private function createPaidPlugin(string $readmeHtml): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'name' => 'acme/paid-plugin',
            'repository_url' => 'https://github.com/acme/paid-plugin',
            'readme_html' => $readmeHtml,
            'license_html' => '<p>License agreement content</p>',
        ]);

        PluginPrice::factory()->regular()->create([
            'plugin_id' => $plugin->id,
            'amount' => 2999,
        ]);

        return $plugin;
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function licenseFileProvider(): array
    {
        return [
            'bare name' => ['LICENSE'],
            'markdown' => ['LICENSE.md'],
            'text' => ['LICENSE.txt'],
            'lowercase' => ['license.md'],
            'british spelling' => ['LICENCE.md'],
            'unlicense' => ['UNLICENSE'],
            'copying' => ['COPYING'],
            'suffixed' => ['LICENSE-MIT.md'],
            'dot slash prefixed' => ['./LICENSE.md'],
            'root prefixed' => ['/LICENSE.md'],
            'github blob url' => ['https://github.com/acme/paid-plugin/blob/main/LICENSE.md'],
            'github raw url' => ['https://raw.githubusercontent.com/acme/paid-plugin/main/LICENSE'],
        ];
    }

    #[DataProvider('licenseFileProvider')]
    public function test_license_links_are_rerouted_to_the_license_page(string $href): void
    {
        $plugin = $this->createPaidPlugin('<p>See the <a href="'.$href.'">license</a>.</p>');

        $this->assertStringContainsString(
            '<a href="'.route('plugins.license', $plugin->routeParams()).'">license</a>',
            $plugin->rendered_readme_html
        );
    }

    public function test_license_links_are_rerouted_when_the_readme_is_rendered(): void
    {
        $plugin = $this->createPaidPlugin('<p>See the <a href="LICENSE.md">license</a>.</p>');

        $this->get(route('plugins.show', $plugin->routeParams()))
            ->assertStatus(200)
            ->assertSee('<a href="'.route('plugins.license', $plugin->routeParams()).'">license</a>', false)
            ->assertDontSee('<a href="LICENSE.md">', false);
    }

    public function test_multiple_license_links_are_all_rerouted(): void
    {
        $plugin = $this->createPaidPlugin(
            '<p><a href="LICENSE">MIT</a> and <a class="x" href=\'./LICENCE.txt\'>terms</a></p>'
        );

        $licenseUrl = route('plugins.license', $plugin->routeParams());

        $this->assertSame(
            '<p><a href="'.$licenseUrl.'">MIT</a> and <a class="x" href=\''.$licenseUrl.'\'>terms</a></p>',
            $plugin->rendered_readme_html
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function nonLicenseLinkProvider(): array
    {
        return [
            'other document' => ['CONTRIBUTING.md'],
            'nested file' => ['docs/LICENSE.md'],
            'anchor' => ['#license'],
            'unrelated script' => ['https://example.com/license-checker.js'],
            'another repository' => ['https://github.com/other/repo/blob/main/LICENSE.md'],
            'repository subdirectory' => ['https://github.com/acme/paid-plugin/blob/main/docs/LICENSE.md'],
            'repository tree' => ['https://github.com/acme/paid-plugin/tree/main/LICENSE.md'],
            'mail link' => ['mailto:license@example.com'],
        ];
    }

    #[DataProvider('nonLicenseLinkProvider')]
    public function test_other_links_are_left_alone(string $href): void
    {
        $plugin = $this->createPaidPlugin('<p><a href="'.$href.'">link</a></p>');

        $this->assertSame(
            '<p><a href="'.$href.'">link</a></p>',
            $plugin->rendered_readme_html
        );
    }

    public function test_license_links_point_at_the_repository_when_there_is_no_license_page(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create([
            'name' => 'acme/free-plugin',
            'repository_url' => 'https://github.com/acme/free-plugin',
            'readme_html' => '<p><a href="LICENSE.md">license</a></p>',
        ]);

        $this->assertSame(
            '<p><a href="https://github.com/acme/free-plugin/blob/main/LICENSE.md">license</a></p>',
            $plugin->rendered_readme_html
        );
    }

    public function test_license_links_are_untouched_without_a_repository_or_license_page(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create([
            'repository_url' => null,
            'readme_html' => '<p><a href="LICENSE.md">license</a></p>',
        ]);

        $this->assertSame('<p><a href="LICENSE.md">license</a></p>', $plugin->rendered_readme_html);
    }

    public function test_readme_without_content_is_left_as_is(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create(['readme_html' => null]);

        $this->assertNull($plugin->rendered_readme_html);
    }
}
