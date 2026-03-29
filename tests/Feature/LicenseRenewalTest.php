<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class LicenseRenewalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);

        config([
            'subscriptions.plans.max.stripe_price_id_eap' => 'price_test_max_eap',
            'subscriptions.plans.max.stripe_price_id_monthly' => 'price_test_max_monthly',
        ]);
    }

    private function createLegacyLicense(?User $user = null): License
    {
        return License::factory()
            ->for($user ?? User::factory()->create())
            ->withoutSubscriptionItem()
            ->active()
            ->max()
            ->create();
    }

    public function test_renewal_page_requires_authentication(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()->get(route('license.renewal', $license->key));

        $response->assertRedirect(route('customer.login'));
    }

    public function test_renewal_page_shows_upgrade_to_ultra_heading(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()
            ->actingAs($license->user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(200);
        $response->assertSee('Upgrade to Ultra');
        $response->assertSee('Early Access Pricing');
    }

    public function test_renewal_page_shows_yearly_and_monthly_options(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()
            ->actingAs($license->user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(200);
        $response->assertSee('$250');
        $response->assertSee('/year');
        $response->assertSee('$35');
        $response->assertSee('/month');
    }

    public function test_renewal_page_does_not_show_license_details(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()
            ->actingAs($license->user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(200);
        $response->assertDontSee('License Key');
        $response->assertDontSee('Current Expiry');
    }

    public function test_renewal_page_does_not_show_what_happens_section(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()
            ->actingAs($license->user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(200);
        $response->assertDontSee('What happens when you renew');
    }

    public function test_renewal_page_does_not_show_same_great_rates(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()
            ->actingAs($license->user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(200);
        $response->assertDontSee('same great rates');
    }

    public function test_renewal_page_uses_dashboard_layout(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->withoutVite()
            ->actingAs($license->user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(200);
        // Dashboard layout includes sidebar with navigation items
        $response->assertSee('Manage Subscription');
    }

    public function test_renewal_page_returns_403_for_other_users_license(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $license = $this->createLegacyLicense($owner);

        $response = $this->withoutVite()
            ->actingAs($otherUser)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(403);
    }

    public function test_renewal_page_returns_404_for_non_legacy_license(): void
    {
        $user = User::factory()->create();
        $license = License::factory()
            ->for($user)
            ->active()
            ->max()
            ->create(); // Has subscription_item_id (not legacy)

        $response = $this->withoutVite()
            ->actingAs($user)
            ->get(route('license.renewal', $license->key));

        $response->assertStatus(404);
    }

    public function test_checkout_requires_billing_period(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->actingAs($license->user)
            ->post(route('license.renewal.checkout', $license->key));

        $response->assertSessionHasErrors('billing_period');
    }

    public function test_checkout_rejects_invalid_billing_period(): void
    {
        $license = $this->createLegacyLicense();

        $response = $this->actingAs($license->user)
            ->post(route('license.renewal.checkout', $license->key), [
                'billing_period' => 'weekly',
            ]);

        $response->assertSessionHasErrors('billing_period');
    }

    public function test_renewal_route_is_under_dashboard_prefix(): void
    {
        $license = $this->createLegacyLicense();

        $url = route('license.renewal', $license->key);

        $this->assertStringContains('/dashboard/license/', $url);
    }

    /**
     * Assert that a string contains a substring.
     */
    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'."
        );
    }
}
