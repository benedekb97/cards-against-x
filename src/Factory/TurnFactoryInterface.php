<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\RoundInterface;
use App\Entity\TurnInterface;

interface TurnFactoryInterface
{
    public function createForRound(RoundInterface $round): TurnInterface;
}