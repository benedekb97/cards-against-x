<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\GameInterface;
use App\Entity\PlayerInterface;
use App\Entity\PlayInterface;
use App\Entity\RoundInterface;
use App\Entity\TurnInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class GameCardService implements GameCardServiceInterface
{
    public function getPlayedWhiteCards(GameInterface $game): Collection
    {
        $collection = new ArrayCollection();

        /** @var RoundInterface $round */
        foreach ($game->getRounds() as $round) {
            /** @var TurnInterface $turn */
            foreach ($round->getTurns() as $turn) {
                /** @var PlayInterface $play */
                foreach ($turn->getPlays() as $play) {
                    $collection->add($play->getCards());
                }
            }
        }

        return $collection;
    }

    public function getDealtWhiteCards(GameInterface $game): Collection
    {
        $collection = new ArrayCollection();

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            $collection->add($player->getCards());
        }

        return $collection;
    }
}