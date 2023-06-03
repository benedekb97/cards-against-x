<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Enum\GameStatus;
use App\Entity\Enum\TurnStatus;
use App\Entity\UserInterface;

class UserCardSubmitActionChecker implements UserCardSubmitActionCheckerInterface
{
    public function check(UserInterface $user): bool
    {
        if (!$user->hasPlayer()) {
            return false;
        }

        if ($user->getPlayer()->getGame()?->getStatus() !== GameStatus::IN_PROGRESS) {
            return false;
        }

        $currentTurn = $user
            ->getPlayer()
            ->getGame()
            ?->getCurrentRound()
            ?->getCurrentTurn();

        if ($currentTurn?->getPlayer() === $user->getPlayer()) {
            return false;
        }

        if ($currentTurn?->getStatus() !== TurnStatus::IN_PROGRESS) {
            return false;
        }

        if ($currentTurn->hasPlayerPlayed($user->getPlayer())) {
            return false;
        }

        return true;
    }
}