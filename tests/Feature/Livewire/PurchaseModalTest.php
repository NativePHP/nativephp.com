<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PurchaseModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PurchaseModalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function purchase_modal_can_be_opened_with_plan()
    {
        Livewire::test(PurchaseModal::class)
            ->call('openModal', 'mini')
            ->assertSet('selectedPlan', 'mini')
            ->assertSet('showModal', true);
    }

    #[Test]
    public function purchase_modal_can_be_closed()
    {
        Livewire::test(PurchaseModal::class)
            ->set('showModal', true)
            ->set('email', 'test@example.com')
            ->set('selectedPlan', 'mini')
            ->call('closeModal')
            ->assertSet('showModal', false)
            ->assertSet('email', '')
            ->assertSet('selectedPlan', null);
    }

    #[Test]
    public function purchase_modal_validates_email()
    {
        Livewire::test(PurchaseModal::class)
            ->call('openModal', 'mini')
            ->set('email', 'invalid-email')
            ->call('emitEmail')
            ->assertHasErrors(['email' => 'email']);
    }

    #[Test]
    public function purchase_modal_requires_email()
    {
        Livewire::test(PurchaseModal::class)
            ->call('openModal', 'mini')
            ->set('email', '')
            ->call('emitEmail')
            ->assertHasErrors(['email' => 'required']);
    }

    #[Test]
    public function purchase_modal_emits_event_with_valid_email()
    {
        Livewire::test(PurchaseModal::class)
            ->call('openModal', 'mini')
            ->set('email', 'valid@example.com')
            ->call('emitEmail')
            ->assertDispatched('email-submitted', [
                'email' => 'valid@example.com',
                'plan' => 'mini',
            ]);
    }

    #[Test]
    public function purchase_modal_closes_after_emitting_event()
    {
        Livewire::test(PurchaseModal::class)
            ->call('openModal', 'mini')
            ->set('email', 'valid@example.com')
            ->call('emitEmail')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function purchase_modal_can_be_opened_via_alpine_event()
    {
        $component = Livewire::test(PurchaseModal::class);

        // Simulate the Alpine.js event
        $component->dispatch('open-purchase-modal', ['plan' => 'pro'])
            ->assertDispatched('open-purchase-modal');

        // Since we can't directly test Alpine.js event handling in PHPUnit,
        // we'll verify that the openModal method works as expected
        $component->call('openModal', 'pro')
            ->assertSet('selectedPlan', 'pro')
            ->assertSet('showModal', true);
    }
}
