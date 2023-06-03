<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\UserInterface;

interface UserWinnerSubmitActionCheckerInterface
{
    public function check(UserInterface $user): bool;
}