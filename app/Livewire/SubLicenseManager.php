<?php

namespace App\Livewire;

use App\Models\License;
use Livewire\Component;

class SubLicenseManager extends Component
{
    public License $license;

    public bool $isPolling = false;

    public int $initialSubLicenseCount;

    public function mount(License $license): void
    {
        $this->license = $license;
        $this->initialSubLicenseCount = $license->subLicenses->count();
    }

    public function startPolling(): void
    {
        $this->isPolling = true;
    }

    public function render()
    {
        // Refresh the license and sublicenses from the database
        $this->license->refresh();
        $this->license->load('subLicenses');

        // Check if a new sublicense has been added
        if ($this->isPolling && $this->license->subLicenses->count() > $this->initialSubLicenseCount) {
            $this->isPolling = false;
            $this->initialSubLicenseCount = $this->license->subLicenses->count();
        }

        $activeSubLicenses = $this->license->subLicenses->where('is_suspended', false);
        $suspendedSubLicenses = $this->license->subLicenses->where('is_suspended', true);

        return view('livewire.sub-license-manager', [
            'activeSubLicenses' => $activeSubLicenses,
            'suspendedSubLicenses' => $suspendedSubLicenses,
        ]);
    }
}
