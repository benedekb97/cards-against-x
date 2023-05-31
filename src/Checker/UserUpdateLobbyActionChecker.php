<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\GameInterface;
use App\Entity\UserInterface;

class UserUpdateLobbyActionChecker implements UserUpdateLobbyActionCheckerInterface
{
    public function check(UserInterface $user, GameInterface $game): bool
    {
        if ($game->getCreatedBy() === $user) {
            return true;
        }

        return false;
    }
}