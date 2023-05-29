<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum CardType: string
{
    case BLACK = 'black';
    case WHITE = 'white';
}
