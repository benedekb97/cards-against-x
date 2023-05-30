<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Enum\GameStatus;
use App\Entity\GameInterface;
use App\Entity\UserInterface;

class UserJoinActionChecker implements UserJoinActionCheckerInterface
{
    public function check(UserInterface $user, GameInterface $game): bool
    {
        if ($game->getStatus() !== GameStatus::LOBBY) {
            return false;
        }

        if (!$user->hasPlayer()) {
            return true;
        }

        return false;
    }
}