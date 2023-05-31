<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\GameInterface;
use App\Entity\RoundInterface;

interface RoundFactoryInterface
{
    public function createForGame(GameInterface $game): RoundInterface;
}