<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\TurnInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TurnEvent extends Event
{
    public function __construct(
        private readonly TurnInterface $turn
    ) {}

    public function getTurn(): TurnInterface
    {
        return $this->turn;
    }
}