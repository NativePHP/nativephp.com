<?php

namespace App\Jobs;

use App\Models\License;
use App\Services\Anystack\Anystack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateAnystackSubLicenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public License $parentLicense,
        public ?string $name = null,
    ) {}

    public function handle(): void
    {
        $licenseData = $this->createSubLicenseInAnystack();

        $subLicense = $this->parentLicense->subLicenses()->create([
            'anystack_id' => $licenseData['id'],
            'name' => $this->name,
            'key' => $licenseData['key'],
            'expires_at' => $licenseData['expires_at'],
        ]);
    }

    private function createSubLicenseInAnystack(): array
    {
        $data = [
            'policy_id' => $this->parentLicense->subscriptionType->anystackPolicyId(),
            'contact_id' => $this->parentLicense->user->anystack_contact_id,
            'parent_license_id' => $this->parentLicense->anystack_id,
        ];

        return Anystack::api()
            ->licenses($this->parentLicense->subscriptionType->anystackProductId())
            ->create($data)
            ->throw()
            ->json('data');
    }
}
