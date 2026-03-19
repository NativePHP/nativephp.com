<?php

namespace App\Actions\Licenses;

use App\Models\License;
use App\Services\Anystack\Anystack;

class DeleteLicense
{
    /**
     * Handle the deletion of a license.
     */
    public function handle(License $license, bool $deleteFromAnystack = true): bool
    {
        if ($deleteFromAnystack) {
            Anystack::api()
                ->license($license->anystack_id, $license->anystack_product_id)
                ->delete();
        }

        return $license->delete();
    }
}
