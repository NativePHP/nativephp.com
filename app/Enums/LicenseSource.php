<?php

namespace App\Enums;

enum LicenseSource: string
{
    case Stripe = 'stripe';
    case Bifrost = 'bifrost';
    case Manual = 'manual';
    case OpenCollective = 'opencollective';
}
