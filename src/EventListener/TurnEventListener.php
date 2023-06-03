<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Enum\TurnStatus;
use App\Entity\PlayerInterface;
use App\Event\RoundEvent;
use App\Event\TurnEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener(event: TurnEvent::class, method: 'onTurnEvent')]
readonly class TurnEventListener
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function onTurnEvent(TurnEvent $event): void
    {
        $turn = $event->getTurn();

        $allPlayersPlayed = true;
        $allPlayersReady = true;

        /** @var PlayerInterface $player */
        foreach ($turn->getRound()->getGame()->getPlayers() as $player) {
            if (!$player->isReady()) {
                $allPlayersReady = false;
            }

            if ($turn->getPlayer() === $player) {
                continue;
            }

            if (!$turn->hasPlayerPlayed($player)) {
                $allPlayersPlayed = false;

                break;
            }
        }

        if ($allPlayersPlayed) {
            if (null === $turn->getWinningPlay()) {
                $turn->setStatus(TurnStatus::CHOOSING);
            } elseif (
                $allPlayersReady
            ) {
                $turn->setStatus(TurnStatus::FINISHED);
            } else {
                $turn->setStatus(TurnStatus::RECAP);
            }
        } else {
            $turn->setStatus(TurnStatus::IN_PROGRESS);
        }

        $this->eventDispatcher->dispatch(new RoundEvent($turn->getRound()));
    }
}