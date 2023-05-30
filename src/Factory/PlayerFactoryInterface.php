<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PlayerInterface;
use App\Entity\UserInterface;

interface PlayerFactoryInterface
{
    public function createForUser(UserInterface $user): PlayerInterface;
}