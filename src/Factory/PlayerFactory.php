<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Player;
use App\Entity\PlayerInterface;
use App\Entity\UserInterface;

class PlayerFactory implements PlayerFactoryInterface
{
    public function createForUser(UserInterface $user): PlayerInterface
    {
        $player = new Player();

        $player->setUser($user);
        $user->setPlayer($player);

        return $player;
    }
}