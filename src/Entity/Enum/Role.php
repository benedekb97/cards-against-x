<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum Role: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
}