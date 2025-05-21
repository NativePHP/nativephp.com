<?php

namespace App\Actions\Licenses;

use App\Models\License;
use App\Services\Anystack\Anystack;

class SuspendLicense
{
    public function __construct(
        protected Anystack $anystack
    ) {}

    /**
     * Handle the suspension of a license.
     */
    public function handle(License $license): License
    {
        $this->anystack->suspendLicense($license->anystack_product_id, $license->anystack_id);

        $license->update([
            'is_suspended' => true,
        ]);

        return $license;
    }
}
