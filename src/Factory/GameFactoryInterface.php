<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\GameInterface;
use App\Entity\PlayerInterface;

interface GameFactoryInterface
{
    public function createForPlayer(PlayerInterface $player): GameInterface;
}