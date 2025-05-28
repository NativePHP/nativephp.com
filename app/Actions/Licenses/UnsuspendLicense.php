<?php

namespace App\Actions\Licenses;

use App\Models\License;
use App\Services\Anystack\Anystack;

class UnsuspendLicense
{
    /**
     * Handle the un-suspension of a license.
     */
    public function handle(License $license): License
    {
        Anystack::api()
            ->license($license->anystack_id, $license->anystack_product_id)
            ->suspend(false);

        $license->update([
            'is_suspended' => false,
        ]);

        return $license;
    }
}
