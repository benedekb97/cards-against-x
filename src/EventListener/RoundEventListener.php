<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Enum\TurnStatus;
use App\Entity\RoundInterface;
use App\Entity\TurnInterface;
use App\Event\RoundEvent;
use App\Event\TurnEvent;
use App\Service\GameCardServiceInterface;
use App\Service\GameServiceInterface;
use App\Service\PlayerCardServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: RoundEvent::class, method: 'onRoundEvent')]
readonly class RoundEventListener
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private PlayerCardServiceInterface $playerCardService,
        private GameServiceInterface $gameService,
        private GameCardServiceInterface $gameCardService
    ) {}

    public function onRoundEvent(RoundEvent $event): void
    {
        $round = $event->getRound();

        if ($round->getCurrentTurn()->getStatus() === TurnStatus::FINISHED) {
            $nextTurn = $this->getNextTurn($round);

            if ($nextTurn === null) {
                $nextRound = $this->getNextRound($round);

                $round->getGame()->setCurrentRound($nextRound);

                $nextTurn = $nextRound->getCurrentTurn();
                $nextTurn->setCard(
                    $this->gameCardService->getBlackCardForGame($round->getGame())
                );

                $this->eventDispatcher->dispatch(new TurnEvent($nextTurn));

                $this->gameService->setPlayersNotReady($round->getGame());

                return;
            }

            $nextTurn->setStatus(TurnStatus::IN_PROGRESS);
            $nextTurn->setCard(
                $this->gameCardService->getBlackCardForGame($round->getGame())
            );

            $round->setCurrentTurn($nextTurn);

            $this->playerCardService->assignCardsToPlayers($round->getGame());

            $this->gameService->setPlayersNotReady($round->getGame());
        }
    }

    private function getNextTurn(RoundInterface $round): ?TurnInterface
    {
        if ($round->getCurrentTurn()->getNumber() === $round->getTurns()->count()) {
            return null;
        }

        /** @var TurnInterface $turn */
        foreach ($round->getTurns() as $turn) {
            if ($turn->getNumber() === $round->getCurrentTurn()->getNumber()+1) {
                return $turn;
            }
        }

        return null;
    }

    private function getNextRound(RoundInterface $currentRound): ?RoundInterface
    {
        if ($currentRound->getNumber() === $currentRound->getGame()->getNumberOfRounds()) {
            // end of game
            return null;
        }

        /** @var RoundInterface $round */
        foreach ($currentRound->getGame()->getRounds() as $round) {
            if ($round->getNumber() === $currentRound->getNumber() + 1) {
                return $round;
            }
        }

        return null;
    }
}