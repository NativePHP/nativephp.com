<?php

namespace App\Features;

class AllowPaidPlugins
{
    public function resolve(mixed $scope): bool
    {
        return true;
    }
}
