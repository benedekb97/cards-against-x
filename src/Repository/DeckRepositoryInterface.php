<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DeckInterface;
use App\Entity\GameInterface;
use Doctrine\Persistence\ObjectRepository;

interface DeckRepositoryInterface extends ObjectRepository
{
    public function getDecksForGame(GameInterface $game): array;

    public function getDeckForGame(GameInterface $game, mixed $id): ?DeckInterface;
}