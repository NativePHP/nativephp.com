<?php

namespace Tests\Feature;

use App\Livewire\MobilePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobilePricingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_users_will_directly_create_checkout_session()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $component = Livewire::test(MobilePricing::class);
        $component->assertSeeHtml([
            // 'wire:click="createCheckoutSession(\'mini\')"',
            'wire:click="createCheckoutSession(\'pro\')"',
            'wire:click="createCheckoutSession(\'max\')"',
        ]);
        $component->assertDontSeeHtml([
            // '@click="$dispatch(\'open-purchase-modal\', { plan: \'mini\' })"',
            '@click="$dispatch(\'open-purchase-modal\', { plan: \'pro\' })"',
            '@click="$dispatch(\'open-purchase-modal\', { plan: \'max\' })"',
        ]);
    }

    #[Test]
    public function guest_users_see_purchase_modal_component()
    {
        Auth::logout();

        Livewire::test(MobilePricing::class)
            ->assertSeeLivewire('purchase-modal')
            ->assertSeeHtml([
                // '@click="$dispatch(\'open-purchase-modal\', { plan: \'mini\' })"',
                '@click="$dispatch(\'open-purchase-modal\', { plan: \'pro\' })"',
                '@click="$dispatch(\'open-purchase-modal\', { plan: \'max\' })"',
            ])
            ->assertDontSeeHtml([
                // 'wire:click="createCheckoutSession(\'mini\')"',
                'wire:click="createCheckoutSession(\'pro\')"',
                'wire:click="createCheckoutSession(\'max\')"',
            ]);
    }

    #[Test]
    public function authenticated_users_do_not_see_purchase_modal_component()
    {
        Auth::login(User::factory()->create());

        Livewire::test(MobilePricing::class)
            ->assertDontSeeLivewire('purchase-modal');
    }

    #[Test]
    public function it_validates_email_before_creating_user()
    {
        Livewire::test(MobilePricing::class)
            ->call('handlePurchaseRequest', ['email' => 'invalid-email'])
            ->assertHasErrors('email');
    }
}
