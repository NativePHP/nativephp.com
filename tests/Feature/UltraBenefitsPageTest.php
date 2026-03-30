<?php

namespace Tests\Feature;

use App\Enums\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\SubscriptionItem;
use Tests\TestCase;

class UltraBenefitsPageTest extends TestCase
{
    use RefreshDatabase;

    private function createUltraSubscriber(): User
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_'.uniqid(),
        ]);

        $subscription = \Laravel\Cashier\Subscription::factory()
            ->for($user)
            ->active()
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'is_comped' => false,
            ]);

        SubscriptionItem::factory()
            ->for($subscription, 'subscription')
            ->create([
                'stripe_price' => Subscription::Max->stripePriceId(),
                'quantity' => 1,
            ]);

        return $user;
    }

    public function test_ultra_subscriber_can_access_benefits_page(): void
    {
        $user = $this->createUltraSubscriber();

        $response = $this->actingAs($user)->get(route('customer.ultra.index'));

        $response->assertStatus(200);
    }

    public function test_non_ultra_user_is_redirected_to_pricing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('customer.ultra.index'));

        $response->assertRedirect(route('pricing'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('customer.ultra.index'));

        $response->assertRedirect(route('customer.login'));
    }

    public function test_benefits_page_displays_expected_content(): void
    {
        $user = $this->createUltraSubscriber();

        $response = $this->actingAs($user)->get(route('customer.ultra.index'));

        $response->assertSee('Ultra');
        $response->assertSee('Your premium subscription benefits');
        $response->assertSee('All first-party plugins');
        $response->assertSee('Claude Code Plugin Dev Kit');
        $response->assertSee('Teams');
        $response->assertSee('Premium support');
        $response->assertSee('Up to 90% Marketplace revenue');
        $response->assertSee('Exclusive discounts');
        $response->assertSee('Direct repo access on GitHub');
        $response->assertSee('Shape the roadmap');
    }

    public function test_benefits_page_uses_config_values(): void
    {
        $user = $this->createUltraSubscriber();

        $response = $this->actingAs($user)->get(route('customer.ultra.index'));

        $includedSeats = config('subscriptions.plans.max.included_seats');
        $extraSeatMonthly = config('subscriptions.plans.max.extra_seat_price_monthly');
        $extraSeatYearly = config('subscriptions.plans.max.extra_seat_price_yearly');

        $response->assertSee("{$includedSeats} seats included");
        $response->assertSee("\${$extraSeatMonthly}/mo");
        $response->assertSee("\${$extraSeatYearly}/mo on annual");
    }
}
