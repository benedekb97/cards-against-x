<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\GameInterface;
use App\Entity\RoundInterface;
use App\Service\GameCardServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class CurrentRoundProcessor implements GameProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameCardServiceInterface $gameCardService
    ) {}

    public function process(GameInterface $game): void
    {
        $currentRound = $game->getCurrentRound();

        if (null === $currentRound) {
            return;
        }

        if (null === $currentRound->getCurrentTurn()) {
            $game->setCurrentRound($round = $this->resolveNextRound($game));

            $round?->getCurrentTurn()?->setCard($this->gameCardService->getBlackCardForGame($game));

            if (null !== $round?->getCurrentTurn()) {
                $this->entityManager->persist($round->getCurrentTurn());
            }

            $this->entityManager->persist($game);
        }
    }

    private function resolveNextRound(GameInterface $game): ?RoundInterface
    {
        $nextRoundNumber = $game->getCurrentRound()->getNumber() + 1;

        return $game->getRound($nextRoundNumber);
    }
}