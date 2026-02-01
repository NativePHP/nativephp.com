<?php

namespace App\Features;

use Stephenjude\FilamentFeatureFlag\Traits\WithFeatureResolver;

class ShowPlugins
{
    use WithFeatureResolver;

    protected bool $defaultValue = true;
}
