<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpgradeInsecureRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public static function pages(): array
    {
        return [
            'main layout' => ['/docs/mobile/3/getting-started/introduction', false],
            'auth layout' => ['/login', false],
            'dashboard layout' => ['/dashboard', true],
        ];
    }

    #[Test]
    #[DataProvider('pages')]
    public function pages_upgrade_insecure_requests_outside_local(string $uri, bool $signedIn): void
    {
        $this->app['env'] = 'production';

        $this->getPage($uri, $signedIn)
            ->assertStatus(200)
            ->assertSee('content="upgrade-insecure-requests"', false);
    }

    #[Test]
    #[DataProvider('pages')]
    public function pages_do_not_upgrade_insecure_requests_locally(string $uri, bool $signedIn): void
    {
        $this->app['env'] = 'local';

        $this->getPage($uri, $signedIn)
            ->assertStatus(200)
            ->assertDontSee('upgrade-insecure-requests', false);
    }

    private function getPage(string $uri, bool $signedIn)
    {
        if ($signedIn) {
            $this->actingAs(User::factory()->create());
        }

        return $this->withoutVite()->get($uri);
    }
}
