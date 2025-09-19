<?php

namespace App\Features;

use Laravel\Pennant\Feature;

class ShowAuthButtons
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): bool
    {
        if ($scope) {
            return Feature::for(null)->active(static::class);
        }

        return false;
    }
}
