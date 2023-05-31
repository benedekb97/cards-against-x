<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Enum\GameStatus;
use App\Entity\UserInterface;

class UserStartActionChecker implements UserStartActionCheckerInterface
{
    public function check(UserInterface $user): bool
    {
        $game = $user->getPlayer()?->getGame();

        if ($game === null) {
            return false;
        }

        if ($game->getCreatedBy() !== $user) {
            return false;
        }

        if (!$game->isReadyToStart()) {
            return false;
        }

        return true;
    }
}