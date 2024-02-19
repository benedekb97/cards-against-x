<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\Enum\GameStatus;
use App\Entity\Enum\TurnStatus;
use App\Entity\GameInterface;
use App\Entity\PlayerInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class PlayerReadyStateProcessor implements GameProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(GameInterface $game): void
    {
        if ($game->getStatus() !== GameStatus::IN_PROGRESS) {
            return;
        }

        $turn = $game->getCurrentRound()?->getCurrentTurn();

        if (null === $turn) {
            return;
        }

        if ($turn->getStatus() !== TurnStatus::CHOOSING) {
            return;
        }

        $this->setPlayersNotReady($game);
    }

    private function setPlayersNotReady(GameInterface $game): void
    {
        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            $player->setReady(false);

            $this->entityManager->persist($player);
        }
    }
}