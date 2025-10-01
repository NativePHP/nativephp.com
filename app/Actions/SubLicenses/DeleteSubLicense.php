<?php

namespace App\Actions\SubLicenses;

use App\Models\SubLicense;
use App\Services\Anystack\Anystack;

class DeleteSubLicense
{
    /**
     * Handle the deletion of a sub-license.
     */
    public function handle(SubLicense $subLicense): bool
    {
        Anystack::api()
            ->license($subLicense->anystack_id, $subLicense->parentLicense->subscriptionType->anystackProductId())
            ->delete();

        return $subLicense->delete();
    }
}
