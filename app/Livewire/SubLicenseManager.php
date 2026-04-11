<?php

namespace App\Livewire;

use App\Jobs\CreateAnystackSubLicenseJob;
use App\Jobs\RevokeMaxAccessJob;
use App\Jobs\UpdateAnystackContactAssociationJob;
use App\Models\License;
use App\Models\SubLicense;
use Flux;
use Livewire\Component;

class SubLicenseManager extends Component
{
    public License $license;

    public bool $isPolling = false;

    public int $initialSubLicenseCount;

    public string $createName = '';

    public string $createAssignedEmail = '';

    public ?int $editingSubLicenseId = null;

    public string $editName = '';

    public string $editAssignedEmail = '';

    public function mount(License $license): void
    {
        $this->license = $license;
        $this->initialSubLicenseCount = $license->subLicenses->count();
    }

    public function openCreateModal(): void
    {
        $this->reset(['createName', 'createAssignedEmail']);
        Flux::modal('create-sub-license')->show();
    }

    public function createSubLicense(): void
    {
        $this->validate([
            'createName' => ['nullable', 'string', 'max:255'],
            'createAssignedEmail' => ['nullable', 'email', 'max:255'],
        ]);

        if (! $this->license->canCreateSubLicense()) {
            return;
        }

        dispatch(new CreateAnystackSubLicenseJob(
            $this->license,
            $this->createName ?: null,
            $this->createAssignedEmail ?: null,
        ));

        $this->isPolling = true;
        $this->reset(['createName', 'createAssignedEmail']);
        Flux::modal('create-sub-license')->close();
    }

    public function editSubLicense(int $subLicenseId): void
    {
        $subLicense = $this->license->subLicenses->firstWhere('id', $subLicenseId);

        if (! $subLicense) {
            return;
        }

        $this->editingSubLicenseId = $subLicenseId;
        $this->editName = $subLicense->name ?? '';
        $this->editAssignedEmail = $subLicense->assigned_email ?? '';

        Flux::modal('edit-sub-license')->show();
    }

    public function updateSubLicense(): void
    {
        $this->validate([
            'editName' => ['nullable', 'string', 'max:255'],
            'editAssignedEmail' => ['nullable', 'email', 'max:255'],
        ]);

        $subLicense = SubLicense::where('id', $this->editingSubLicenseId)
            ->where('parent_license_id', $this->license->id)
            ->firstOrFail();

        $oldEmail = $subLicense->assigned_email;

        $subLicense->update([
            'name' => $this->editName ?: null,
            'assigned_email' => $this->editAssignedEmail ?: null,
        ]);

        if ($oldEmail !== ($this->editAssignedEmail ?: null) && $this->editAssignedEmail) {
            dispatch(new UpdateAnystackContactAssociationJob($subLicense, $this->editAssignedEmail));
        }

        if ($oldEmail && $oldEmail !== ($this->editAssignedEmail ?: null) && $this->license->policy_name === 'max') {
            dispatch(new RevokeMaxAccessJob($oldEmail));
        }

        $this->reset(['editingSubLicenseId', 'editName', 'editAssignedEmail']);
        Flux::modal('edit-sub-license')->close();
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
