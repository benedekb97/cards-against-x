<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\RoundInterface;
use App\Entity\Turn;
use App\Entity\TurnInterface;

class TurnFactory implements TurnFactoryInterface
{
    public function createForRound(RoundInterface $round): TurnInterface
    {
        $turn = new Turn();

        $round->addTurn($turn);

        return $turn;
    }
}