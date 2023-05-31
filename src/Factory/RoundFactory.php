<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\GameInterface;
use App\Entity\Round;
use App\Entity\RoundInterface;

class RoundFactory implements RoundFactoryInterface
{
    public function createForGame(GameInterface $game): RoundInterface
    {
        $round = new Round();

        $game->addRound($round);

        return $round;
    }
}