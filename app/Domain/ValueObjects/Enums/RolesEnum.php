<?php

namespace App\Domain\ValueObjects\Enums;

enum RolesEnum:string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case USER = 'user';
    case MANAGER = 'manager';
    case SUPPORT = 'support';
    case SUPERVISOR = 'supervisor';
}
