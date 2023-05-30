<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\GameInterface;
use Doctrine\Persistence\ObjectRepository;

interface GameRepositoryInterface extends ObjectRepository
{
    public function findOneBySlug(string $slug): ?GameInterface;
}