<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionExpansionMode: string
{
    case Flag = 'flag';
    case Gate = 'gate';
}
