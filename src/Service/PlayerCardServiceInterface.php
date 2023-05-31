<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\GameInterface;

interface PlayerCardServiceInterface
{
    public function assignCardsToPlayers(GameInterface $game): void;
}