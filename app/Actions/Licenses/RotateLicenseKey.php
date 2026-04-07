<?php

namespace App\Actions\Licenses;

use App\Models\License;
use App\Services\Anystack\Anystack;

class RotateLicenseKey
{
    /**
     * Rotate a license key by creating a new Anystack license
     * and suspending the old one.
     */
    public function handle(License $license): License
    {
        $newLicenseData = $this->createNewAnystackLicense($license);

        $oldAnystackId = $license->anystack_id;

        $license->update([
            'anystack_id' => $newLicenseData['id'],
            'key' => $newLicenseData['key'],
        ]);

        $this->suspendOldAnystackLicense($license, $oldAnystackId);

        return $license;
    }

    private function createNewAnystackLicense(License $license): array
    {
        return Anystack::api()
            ->licenses($license->anystack_product_id)
            ->create([
                'policy_id' => $license->subscriptionType->anystackPolicyId(),
                'contact_id' => $license->user->anystack_contact_id,
            ])
            ->json('data');
    }

    private function suspendOldAnystackLicense(License $license, string $oldAnystackId): void
    {
        Anystack::api()
            ->license($oldAnystackId, $license->anystack_product_id)
            ->suspend();
    }
}
