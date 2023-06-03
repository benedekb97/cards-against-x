<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PlayerInterface;
use App\Entity\PlayInterface;
use App\Entity\TurnInterface;

interface PlayFactoryInterface
{
    public function createForPlayerAndTurn(PlayerInterface $player, TurnInterface $turn): PlayInterface;
}