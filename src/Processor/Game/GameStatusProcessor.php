<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\Enum\GameStatus;
use App\Entity\GameInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class GameStatusProcessor implements GameProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    public function process(GameInterface $game): void
    {
        if ($game->getRounds()->isEmpty()) {
            $game->setStatus(GameStatus::LOBBY);

            $this->entityManager->persist($game);

            return;
        }

        if (null === $game->getCurrentRound()) {
            $game->setStatus(GameStatus::FINISHED);

            $this->entityManager->persist($game);
        }
    }
}