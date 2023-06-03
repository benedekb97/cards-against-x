<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\GameInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GameUpdateEvent extends Event
{
    public function __construct(
        private readonly GameInterface $game
    ) {}

    public function getGame(): GameInterface
    {
        return $this->game;
    }
}