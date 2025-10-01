<?php

namespace App\Livewire;

use App\Models\License;
use Livewire\Component;

class LicenseRenewalSuccess extends Component
{
    public License $license;

    public string $sessionId;

    public bool $renewalCompleted = false;

    public bool $renewalFailed = false;

    public string $originalExpiryDate;

    public function mount(License $license)
    {
        $this->license = $license;
        $this->sessionId = request()->get('session_id');
        $this->originalExpiryDate = $this->license->expires_at?->format('F j, Y') ?? '';

        if ($this->license->expires_at->lt(now()->addDays(30))) {
            $this->checkRenewalStatus();
        } else {
            $this->renewalCompleted = true;
        }
    }

    public function checkRenewalStatus()
    {
        // Refresh the license from database
        $this->license->refresh();

        // Check if renewal is complete
        $hasSubscription = ! is_null($this->license->subscription_item_id);

        // Check if expiry was updated recently (within last 15 minutes) and is different from original
        $expiryUpdatedRecently = $this->license->updated_at > now()->subMinutes(15);
        $expiryChanged = $this->originalExpiryDate !== ($this->license->expires_at?->format('F j, Y') ?? '');

        if ($hasSubscription && $expiryUpdatedRecently && $expiryChanged) {
            $this->renewalCompleted = true;
        } elseif ($this->license->updated_at > now()->subMinutes(30) && ! $hasSubscription) {
            // If it's been over 30 minutes and still no subscription, consider it failed
            $this->renewalFailed = true;
        }
    }

    public function render()
    {
        return view('livewire.license-renewal-success')
            ->layout('components.layout', ['title' => 'Renewal Successful']);
    }
}
