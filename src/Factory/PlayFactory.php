<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Play;
use App\Entity\PlayerInterface;
use App\Entity\PlayInterface;
use App\Entity\TurnInterface;

class PlayFactory implements PlayFactoryInterface
{
    public function createForPlayerAndTurn(PlayerInterface $player, TurnInterface $turn): PlayInterface
    {
        $play = new Play();

        $player->addPlay($play);
        $turn->addPlay($play);

        return $play;
    }
}