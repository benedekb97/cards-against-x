<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Enum\GameStatus;
use App\Entity\UserInterface;

readonly class UserHostEligibilityChecker implements UserHostEligibilityCheckerInterface
{
    public function check(UserInterface $user): bool
    {
        if (!$user->hasPlayer()) {
            return true;
        }

        if ($user->getPlayer()->getGame()->getStatus() === GameStatus::FINISHED) {
            $user->setPlayer(null);

            return true;
        }

        return false;
    }
}