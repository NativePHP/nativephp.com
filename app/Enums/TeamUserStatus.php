<?php

namespace App\Enums;

enum TeamUserStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Removed = 'removed';
}
