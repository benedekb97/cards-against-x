<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\Enum\TurnStatus;
use App\Entity\GameInterface;
use App\Entity\PlayerInterface;
use App\Entity\TurnInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class TurnStatusProcessor implements GameProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(GameInterface $game): void
    {
        $turn = $game->getCurrentRound()?->getCurrentTurn();

        if (null === $turn) {
            return;
        }

        if ($this->allPlayersReady($game)) {
            $turn->setStatus(TurnStatus::FINISHED);

            $this->entityManager->persist($turn);

            return;
        }

        if ($this->hasWinner($turn)) {
            $turn->setStatus(TurnStatus::RECAP);

            $this->entityManager->persist($turn);

            return;
        }

        if ($this->haveAllPLayersPlayed($game)) {
            $turn->setStatus(TurnStatus::CHOOSING);

            $this->entityManager->persist($turn);

            return;
        }

        $turn->setStatus(TurnStatus::IN_PROGRESS);

        $this->entityManager->persist($turn);
    }

    private function haveAllPLayersPlayed(GameInterface $game): bool
    {
        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            if ($player === $game->getCurrentRound()->getCurrentTurn()->getPlayer()) {
                continue;
            }

            if (!($game?->getCurrentRound()?->getCurrentTurn()?->hasPlayerPlayed($player) ?? false)) {
                return false;
            }
        }

        return true;
    }

    private function allPlayersReady(GameInterface $game): bool
    {
        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            if (!$player->isReady()) {
                return false;
            }
        }

        return true;
    }

    private function hasWinner(TurnInterface $turn): bool
    {
        return null !== $turn->getWinningPlay();
    }
}