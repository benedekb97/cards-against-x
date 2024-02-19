<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\GameInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class GameFlushProcessor implements GameProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(GameInterface $game): void
    {
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }
}