<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Enum\GameStatus;
use App\Entity\UserInterface;

class UserLeaveActionChecker implements UserLeaveActionCheckerInterface
{
    public function check(UserInterface $user): bool
    {
        if (!$user->hasPlayer()) {
            return true;
        }

        if ($user->getPlayer()->getGame()->getStatus() !== GameStatus::IN_PROGRESS) {
            return true;
        }

        return false;
    }
}