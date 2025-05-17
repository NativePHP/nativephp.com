<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PurchaseModal;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PurchaseModalTest extends TestCase
{
    #[Test]
    public function purchase_modal_can_set_a_plan()
    {
        Livewire::test(PurchaseModal::class)
            ->call('setPlan', 'mini')
            ->assertSet('selectedPlan', 'mini');
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
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    #[Test]
    public function purchase_modal_requires_email()
    {
        Livewire::test(PurchaseModal::class)
            ->set('email', '')
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    #[Test]
    public function test_submit_action()
    {
        Livewire::test(PurchaseModal::class)
            ->call('setPlan', 'mini')
            ->set('email', 'valid@example.com')
            ->call('submit')
            ->assertDispatched('purchase-request-submitted', [
                'email' => 'valid@example.com',
                'plan' => 'mini',
            ]);
    }

    #[Test]
    public function purchase_modal_closes_after_emitting_event()
    {
        Livewire::test(PurchaseModal::class)
            ->call('setPlan', 'mini')
            ->set('email', 'valid@example.com')
            ->call('submit')
            ->assertSet('showModal', false);
    }
}
