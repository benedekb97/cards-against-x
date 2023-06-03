<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Turn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TurnRepository extends ServiceEntityRepository implements TurnRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Turn::class);
    }
}