<?php

namespace App\Actions\Licenses;

use App\Models\License;
use App\Services\Anystack\Anystack;

class DeleteLicense
{
    public function __construct(
        protected Anystack $anystack
    ) {}

    /**
     * Handle the deletion of a license.
     */
    public function handle(License $license, bool $deleteFromAnystack = true): bool
    {
        if ($deleteFromAnystack) {
            $this->anystack->deleteLicense($license->anystack_product_id, $license->anystack_id);
        }

        return $license->delete();
    }
}
