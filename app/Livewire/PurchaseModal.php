<?php

namespace App\Livewire;

use Livewire\Attributes\Renderless;
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

    #[Renderless]
    public function setPlan(string $plan): void
    {
        $this->selectedPlan = $plan;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset('email', 'selectedPlan');
        $this->resetValidation();
    }

    public function submit(): void
    {
        $this->validate();

        $this->dispatch('purchase-request-submitted', [
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
