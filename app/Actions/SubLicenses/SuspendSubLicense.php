<?php

namespace App\Actions\SubLicenses;

use App\Models\SubLicense;
use App\Services\Anystack\Anystack;

class SuspendSubLicense
{
    /**
     * Handle the suspension of a sub-license.
     */
    public function handle(SubLicense $subLicense): SubLicense
    {
        Anystack::api()
            ->license($subLicense->anystack_id, $subLicense->parentLicense->subscriptionType->anystackProductId())
            ->suspend();

        $subLicense->update([
            'is_suspended' => true,
        ]);

        return $subLicense;
    }
}
