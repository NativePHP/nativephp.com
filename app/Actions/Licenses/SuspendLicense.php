<?php

namespace App\Actions\Licenses;

use App\Models\License;
use App\Services\Anystack\Anystack;

class SuspendLicense
{
    /**
     * Handle the suspension of a license.
     */
    public function handle(License $license): License
    {
        Anystack::api()
            ->license($license->anystack_id, $license->anystack_product_id)
            ->suspend();

        $license->update([
            'is_suspended' => true,
        ]);

        return $license;
    }
}
