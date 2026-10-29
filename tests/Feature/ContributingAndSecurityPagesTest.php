<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContributingAndSecurityPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, false);
        Feature::define(ShowPlugins::class, true);
    }

    #[Test]
    public function the_contributing_page_covers_the_workflow_guidelines_and_code_of_conduct(): void
    {
        $this
            ->withoutVite()
            ->get('/contributing')
            ->assertOk()
            ->assertSee('<title>Contributing - NativePHP</title>', false)
            ->assertSeeInOrder([
                'Contributing to NativePHP',
                'Where to contribute',
                'How to contribute',
                'Pull request guidelines',
                'Code style and tests',
                'Reporting bugs',
                'Security vulnerabilities',
                'License',
                'Code of Conduct',
            ]);
    }

    #[Test]
    public function the_contributing_page_links_to_each_repository_and_the_security_policy(): void
    {
        $this
            ->withoutVite()
            ->get('/contributing')
            ->assertOk()
            ->assertSee('https://github.com/NativePHP/mobile-air', false)
            ->assertSee('https://github.com/NativePHP/desktop', false)
            ->assertSee('https://github.com/NativePHP/nativephp.com', false)
            ->assertSee('<a href="'.route('security').'">security policy</a>', false);
    }

    #[Test]
    public function the_security_page_links_to_the_github_vulnerability_form_for_every_public_package(): void
    {
        $repositories = [
            'mobile-air',
            'desktop',
            'mobile-browser',
            'mobile-camera',
            'mobile-clipboard',
            'mobile-device',
            'mobile-dialog',
            'mobile-dynamic-notifications',
            'mobile-file',
            'mobile-media-player',
            'mobile-microphone',
            'mobile-network',
            'mobile-ota',
            'mobile-share',
            'mobile-system',
            'mobile-ui',
            'mobile-vibe',
            'php-bin',
            'laravel-cloud-deploy',
            'web-ui',
            'mobile-starter',
            'superstack',
        ];

        $response = $this
            ->withoutVite()
            ->get('/security')
            ->assertOk()
            ->assertSee('<title>Security - NativePHP</title>', false)
            ->assertSee('Security Policy');

        foreach ($repositories as $repository) {
            $response->assertSee("https://github.com/NativePHP/{$repository}/security/advisories/new", false);
        }

        $this->assertSame(
            count($repositories),
            substr_count($response->getContent(), '/security/advisories/new'),
        );
    }

    #[Test]
    public function the_security_page_points_the_core_mobile_package_at_its_actual_repository(): void
    {
        $this
            ->withoutVite()
            ->get('/security')
            ->assertOk()
            ->assertSee('nativephp/mobile')
            ->assertSee('https://github.com/NativePHP/mobile-air/security/advisories/new', false)
            ->assertDontSee('https://github.com/NativePHP/mobile/security/advisories/new', false);
    }

    #[Test]
    public function the_security_page_sends_private_services_to_support_and_third_party_plugins_to_their_authors(): void
    {
        $this
            ->withoutVite()
            ->get('/security')
            ->assertOk()
            ->assertSeeInOrder([
                'Bifrost, this website and private plugins',
                'mailto:support@nativephp.com',
                'Third-party plugins',
                '<a href="'.route('plugins').'">plugin directory</a>',
            ], false);
    }

    #[Test]
    public function the_footer_links_to_the_contributing_and_security_pages(): void
    {
        $this
            ->withoutVite()
            ->get('/privacy-policy')
            ->assertOk()
            ->assertSee('href="'.route('contributing').'"', false)
            ->assertSee('href="'.route('security').'"', false);
    }
}
