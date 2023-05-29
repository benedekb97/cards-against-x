<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum GameStatus: string
{
    case LOBBY = 'lobby';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
}