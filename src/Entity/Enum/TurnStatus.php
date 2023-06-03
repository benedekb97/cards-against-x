<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum TurnStatus: string
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case CHOOSING = 'choosing';
    case RECAP = 'recap';
    case FINISHED = 'finished';
}