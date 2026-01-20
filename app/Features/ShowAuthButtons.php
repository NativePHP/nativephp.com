<?php

namespace App\Features;

use Stephenjude\FilamentFeatureFlag\Traits\WithFeatureResolver;

class ShowAuthButtons
{
    use WithFeatureResolver;

    protected bool $defaultValue = false;
}
