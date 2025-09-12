<?php

namespace App\Actions\SubLicenses;

use App\Models\SubLicense;
use App\Services\Anystack\Anystack;

class UnsuspendSubLicense
{
    /**
     * Handle the un-suspension of a sub-license.
     */
    public function handle(SubLicense $subLicense): SubLicense
    {
        Anystack::api()
            ->license($subLicense->anystack_id, $subLicense->parentLicense->subscriptionType->anystackProductId())
            ->suspend(false);

        $subLicense->update([
            'is_suspended' => false,
        ]);

        return $subLicense;
    }
}
