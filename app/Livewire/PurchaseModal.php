<?php

namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;

class PurchaseModal extends Component
{
    public bool $showModal = false;

    #[Validate]
    public string $email = '';

    public ?string $selectedPlan = null;

    protected $rules = [
        'email' => 'required|email',
    ];

    public function openModal($plan): void
    {
        $this->selectedPlan = $plan;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset('email', 'selectedPlan');
        $this->resetValidation();
    }

    public function emitEmail()
    {
        $this->validate();

        $this->dispatch('email-submitted', [
            'email' => $this->email,
            'plan' => $this->selectedPlan,
        ]);

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.purchase-modal');
    }
}
