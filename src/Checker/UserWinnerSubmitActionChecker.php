<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Enum\TurnStatus;
use App\Entity\UserInterface;

class UserWinnerSubmitActionChecker implements UserWinnerSubmitActionCheckerInterface
{
    public function check(UserInterface $user): bool
    {
        $turn = $user->getPlayer()?->getGame()?->getCurrentRound()?->getCurrentTurn();

        if ($turn === null) {
            return false;
        }

        if ($turn->getPlayer() !== $user->getPlayer()) {
            return false;
        }

        if ($turn->getStatus() !== TurnStatus::CHOOSING) {
            return false;
        }

        return true;
    }
}