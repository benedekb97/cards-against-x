<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\GameInterface;
use App\Entity\UserInterface;

interface UserUpdateLobbyActionCheckerInterface
{
    public function check(UserInterface $user, GameInterface $game): bool;
}