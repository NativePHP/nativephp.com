<?php

namespace App\Features;

use Stephenjude\FilamentFeatureFlag\Traits\WithFeatureResolver;

class AllowPaidPlugins
{
    use WithFeatureResolver;

    protected bool $defaultValue = false;
}
