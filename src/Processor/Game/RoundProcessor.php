<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\Enum\TurnStatus;
use App\Entity\GameInterface;
use App\Entity\RoundInterface;
use App\Entity\TurnInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class RoundProcessor implements GameProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(GameInterface $game): void
    {
        $round = $game->getCurrentRound();

        if (null === $round) {
            return;
        }

        if ($round->getCurrentTurn()?->getStatus() === TurnStatus::FINISHED) {
            $round->setCurrentTurn($turn = $this->resolveNextTurn($round));

            if (null !== $turn) {
                $turn->setStatus(TurnStatus::IN_PROGRESS);

                $this->entityManager->persist($turn);
            }

            $this->entityManager->persist($round);
        }
    }

    private function resolveNextTurn(RoundInterface $round): ?TurnInterface
    {
        $nextTurnNumber = $round->getCurrentTurn()->getNumber() + 1;

        return $round->getTurn($nextTurnNumber);
    }
}