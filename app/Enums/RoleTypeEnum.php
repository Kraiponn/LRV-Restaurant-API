<?php

namespace App\Enums;

enum RoleTypeEnum: string
{
    case GUEST = 'guest';
    case MEMBER = 'member';
    case MANAGER =  'manager';
    case ADMIN =    'admin';
}
