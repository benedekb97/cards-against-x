<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CardInterface;
use App\Entity\GameInterface;
use Doctrine\Common\Collections\Collection;

interface GameCardServiceInterface
{
    public function getPlayedWhiteCards(GameInterface $game): Collection;

    public function getDealtWhiteCards(GameInterface $game): Collection;

    public function getBlackCardForGame(GameInterface $game): CardInterface;
}