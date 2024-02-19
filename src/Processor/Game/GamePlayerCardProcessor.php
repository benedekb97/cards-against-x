<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\Enum\TurnStatus;
use App\Entity\GameInterface;
use App\Service\PlayerCardServiceInterface;

readonly class GamePlayerCardProcessor implements GameProcessorInterface
{
    public function __construct(
        private PlayerCardServiceInterface $playerCardService
    ) {}

    public function process(GameInterface $game): void
    {
        $currentTurn = $game->getCurrentRound()?->getCurrentTurn();

        if (null === $currentTurn) {
            return;
        }

        if ($currentTurn->getStatus() === TurnStatus::CHOOSING) {
            $this->playerCardService->assignCardsToPlayers($game);
        }
    }
}