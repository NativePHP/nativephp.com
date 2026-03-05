<?php

namespace App\Enums;

enum TeamUserRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}
