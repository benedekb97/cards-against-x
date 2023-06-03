<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Enum\TurnStatus;
use App\Event\GameUpdateEvent;
use App\Event\RoundEvent;
use App\Message\EndTurnMessage;
use App\Repository\TurnRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class EndTurnMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private TurnRepositoryInterface $turnRepository
    ) {}

    public function __invoke(EndTurnMessage $endTurnMessage): void
    {
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

        $this->eventDispatcher->dispatch(new RoundEvent($turn->getRound()));
        $this->eventDispatcher->dispatch(new GameUpdateEvent($turn->getRound()->getGame()));

        $this->entityManager->flush();
    }
}