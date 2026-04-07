<?php

namespace App\Livewire\Customer\Licenses;

use App\Actions\Licenses\RotateLicenseKey;
use App\Models\License;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('License Details')]
class Show extends Component
{
    public License $license;

    public bool $showEditNameModal = false;

    #[Validate('nullable|string|max:255')]
    public ?string $licenseName = null;

    public function mount(string $licenseKey): void
    {
        $this->license = auth()->user()->licenses()
            ->with('subLicenses')
            ->where('key', $licenseKey)
            ->firstOrFail();

        $this->licenseName = $this->license->name;
    }

    public function updateLicenseName(): void
    {
        $this->validate();

        $this->license->update([
            'name' => $this->licenseName ?: null,
        ]);

        $this->license->refresh();
        $this->showEditNameModal = false;

        session()->flash('success', 'License name updated successfully!');
    }

    public function rotateLicenseKey(RotateLicenseKey $action): void
    {
        if ($this->license->is_suspended || ($this->license->expires_at && $this->license->expires_at->isPast())) {
            session()->flash('error', 'Cannot rotate a suspended or expired license key.');

            return;
        }

        $action->handle($this->license);

        session()->flash('success', 'Your license key has been rotated. Please update any applications using the old key.');

        $this->redirectRoute('customer.licenses.show', $this->license->key);
    }

    public function render()
    {
        return view('livewire.customer.licenses.show');
    }
}
