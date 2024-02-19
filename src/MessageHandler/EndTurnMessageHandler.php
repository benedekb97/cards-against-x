<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Enum\TurnStatus;
use App\Entity\TurnInterface;
use App\Message\EndTurnMessage;
use App\Processor\Game\GameProcessor;
use App\Repository\TurnRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class EndTurnMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TurnRepositoryInterface $turnRepository,
        private GameProcessor $gameProcessor
    ) {}

    public function __invoke(EndTurnMessage $endTurnMessage): void
    {
        /** @var TurnInterface $turn */
        $turn = $this->turnRepository->find($endTurnMessage->getTurnId());

        if ($turn === null) {
            return;
        }

        if (
            $turn !== $turn->getRound()->getCurrentTurn() ||
            $turn->getRound() !== $turn->getRound()->getGame()->getCurrentRound()
        ) {
            return;
        }

        $turn->setStatus(TurnStatus::FINISHED);

        $this->entityManager->persist($turn);

        $this->gameProcessor->process($turn->getRound()->getGame());
    }
}