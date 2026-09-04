<?php

namespace Tests\Feature\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConsolidatedRedirectsTest extends TestCase
{
    use RefreshDatabase;

    public static function renamedPageProvider(): array
    {
        return [
            'super-native root' => ['docs/mobile/4/super-native', '/docs/mobile/4/architecture/super-native'],
            'architecture overview' => ['docs/mobile/4/architecture/overview', '/docs/mobile/4/architecture/super-native'],
            'the-basics navigation' => ['docs/mobile/4/the-basics/navigation', '/docs/mobile/4/the-basics/routing'],
            'edge-components screen' => ['docs/mobile/4/edge-components/screen', '/docs/mobile/4/edge-components/layout'],
            'edge-components card' => ['docs/mobile/4/edge-components/card', '/docs/mobile/4/edge-components/layout'],
        ];
    }

    #[Test]
    #[DataProvider('renamedPageProvider')]
    public function it_redirects_a_renamed_v4_page_via_config_driven_resolution(string $from, string $to): void
    {
        $this->get($from)->assertRedirect($to)->assertStatus(301);
    }

    #[Test]
    public function core_plugin_pages_still_redirect_to_the_basics_ahead_of_the_marketplace_catch_all(): void
    {
        $this->get('docs/mobile/4/plugins/core/device')->assertRedirect('/docs/mobile/4/the-basics/device');
        $this->get('docs/mobile/4/plugins/core/dialog')->assertRedirect('/docs/mobile/4/the-basics/dialogs');
    }

    #[Test]
    public function other_core_plugin_pages_still_fall_through_to_the_marketplace(): void
    {
        $this->get('docs/mobile/4/plugins/core/camera')->assertRedirect('/plugins/nativephp/mobile-camera');
    }
}
