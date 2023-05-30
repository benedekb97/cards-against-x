<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Game;
use App\Entity\GameInterface;
use App\Entity\PlayerInterface;

readonly class GameFactory implements GameFactoryInterface
{
    public function createForPlayer(PlayerInterface $player): GameInterface
    {
        $game = new Game();

        $game->setCreatedBy($player->getUser());
        $game->addPlayer($player);

        return $game;
    }
}