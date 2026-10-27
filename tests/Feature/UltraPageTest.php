<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\Plugin;
use App\Models\PluginPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UltraPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, false);
    }

    #[Test]
    public function it_shows_the_feature_sections_then_the_pricing_card_then_the_faqs(): void
    {
        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertSeeInOrder([
                'Get Ultra',
                'Every first-party plugin. Included.',
                'Claude Code Plugin Dev Kit',
                'Up to 90% revenue share',
                'Bring your whole team',
                'Premium support',
                'Direct repo access',
                'Shape the roadmap',
                'Ready to go Ultra?',
                'Frequently Asked Questions',
            ]);
    }

    #[Test]
    public function it_lists_included_first_party_plugins_with_their_combined_value(): void
    {
        $camera = Plugin::factory()->approved()->paid()->create(['is_official' => true, 'name' => 'nativephp/camera-pro']);
        $scanner = Plugin::factory()->approved()->paid()->create(['is_official' => true, 'name' => 'nativephp/scanner-pro']);
        PluginPrice::factory()->regular()->amount(29900)->for($camera)->create();
        PluginPrice::factory()->regular()->amount(19900)->for($scanner)->create();

        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertSee('nativephp/camera-pro')
            ->assertSee('nativephp/scanner-pro')
            ->assertSee('$498')
            ->assertSee('Ultra pays for itself');
    }

    #[Test]
    public function it_excludes_third_party_free_and_unapproved_plugins(): void
    {
        Plugin::factory()->approved()->paid()->create(['is_official' => false, 'name' => 'acme/third-party']);
        Plugin::factory()->approved()->free()->create(['is_official' => true, 'name' => 'nativephp/free-one']);
        Plugin::factory()->pending()->paid()->create(['is_official' => true, 'name' => 'nativephp/pending-one']);
        Plugin::factory()->approved()->paid()->inactive()->create(['is_official' => true, 'name' => 'nativephp/inactive-one']);

        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertDontSee('acme/third-party')
            ->assertDontSee('nativephp/free-one')
            ->assertDontSee('nativephp/pending-one')
            ->assertDontSee('nativephp/inactive-one');
    }

    #[Test]
    public function it_hides_the_value_callout_when_plugins_are_worth_less_than_a_year_of_ultra(): void
    {
        $plugin = Plugin::factory()->approved()->paid()->create(['is_official' => true]);
        PluginPrice::factory()->regular()->amount(4900)->for($plugin)->create();

        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertSee('$49')
            ->assertDontSee('Ultra pays for itself');
    }

    #[Test]
    public function it_still_renders_without_any_first_party_plugins(): void
    {
        $this
            ->withoutVite()
            ->get(route('pricing'))
            ->assertOk()
            ->assertSee('Every premium plugin from the NativePHP team')
            ->assertDontSee('Ultra pays for itself');
    }
}
