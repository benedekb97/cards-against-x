<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\GameInterface;

interface HasGameInterface
{
    public function getGame(): ?GameInterface;

    public function setGame(?GameInterface $game): void;
}